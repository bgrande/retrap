'use strict';

const FROM_KEY = 'from';
const ANSWER_PREFIX = 'answer-q-';
const ANSWER_KEY = 'answers';
const QUESTION_KEY = 'questions';
const RESULT_KEY = 'results';
const CALC_RESULT_KEY = 'calculated';

const getPrefix = function getPrefix() {
  if (location.pathname === '/') {
    return '/';
  }

  const path = location.pathname.split('/'),
        length = path.length;

  let prefix = '/';

  path.forEach(function (p, i) {
    if (p !== '' && i < (length - 1)) {
      prefix += p + '/';
    }
  });

  return prefix;
}

/**
 * @returns {Object}
 */
async function getQuestions() {
    return await fetch(getPrefix() + 'data/q/en/questions.json')
        .then(function (response) {
        if (response.ok) {
          return response.json();
        }

        return Promise.reject(response);
    })
    .then(function(jsonData) {
      if (jsonData && jsonData.questions) {
          localStorage.setItem(QUESTION_KEY, JSON.stringify(jsonData.questions));
          let keys = Object.keys(jsonData.questions);
          let qCountElement = document.getElementById('js-question-count');

          qCountElement.innerText = keys.length;
      }
    })
    .catch(function(error) {
      console.warn('something went wrong: ', error);
    });
}

async function getResult() {
  return await fetch(getPrefix() + 'data/q/en/result.json')
    .then(function (response) {
      if (response.ok) {
        return response.json();
      }

      return Promise.reject(response);
    })
    .then(function(jsonData) {
      if (jsonData) {
        localStorage.setItem(RESULT_KEY, JSON.stringify(jsonData));
      }
    })
    .catch(function(error) {
      console.warn('something went wrong: ', error);
    });
}

async function sendAnswers() {
  return await fetch(getPrefix() + 'receive.php', {
    method: 'POST',
    body: JSON.stringify({
      answers: JSON.parse(localStorage.getItem(ANSWER_KEY)),
      calculated: JSON.parse(localStorage.getItem(CALC_RESULT_KEY)),
      from: localStorage.getItem(FROM_KEY)
    })
  })
    .then(function (response) {
      if (response.ok) {
        return response.json();
      }

      return Promise.reject(response);
    })
    .then(function(jsonData) {
      // for now nothing
      // @todo trigger result calculation and result page
    })
    .catch(function(error) {
      console.warn('something went wrong: ', error);
    });
}

const createAnswerMarkup = function createAnswerMarkup(question, questionCount) {
      const defaultType = 'radio';

      let listOptions = '',
          checkedValue,
          answers = JSON.parse(localStorage.getItem(ANSWER_KEY)),
          answerKey = ANSWER_PREFIX + questionCount,
          allowedTypes = ['text', 'email', 'textarea', defaultType];

      if (answers.hasOwnProperty(answerKey)) {
          checkedValue = parseInt(answers[answerKey]);
      }

      question.answers.forEach(function (value) {
        let checked = (checkedValue === value.value) ? 'checked' : '',
            type = value.type || defaultType,
            valueText = type === defaultType ? value.value : '',
            classes = type === defaultType ? ' simple' : '',
            placeholder = type === defaultType ? '' : 'placeholder="' + value.text + '"',
            appendix = type === defaultType ? '<span class="checkmark"></span></label>' : '';

        if (!allowedTypes.includes(type)) {
            type = defaultType;
        }

        listOptions += `<li><label><input name="${answerKey}" value="${valueText}" type="${type}" class="answer${classes}" ${placeholder} ${checked}> ${value.text}${appendix}</li>\n`;
      });

      return listOptions;
};

const updateSaverBox = function updateSaverBox(element, questions, currentQuestionNumber) {
  let keys = Object.keys(questions),
      totalQuestionCount = keys.length,
      currentQuestionNumberView = parseInt(currentQuestionNumber) + 1;

  if (element) {
    element.innerText = `Question ${currentQuestionNumberView}/${totalQuestionCount}`
    return true;
  }

  return false;
};

const updateQuestionTitle = function updateQuestionTitle(element, question) {
  if (element) {
    element.innerText = question.question;
    return true;
  }

  return false;
};

const updateAnswers = function updateAnswers(element, question, count) {
  if (element) {
    element.className = 'question-list';
    element.innerHTML = createAnswerMarkup(question, count);

    return true;
  }

  return false;
};

const setHash = function setHash(currentQuestionNumber) {
    // @todo we also need to save and replay the state based on the hash!
    window.location.hash = 'question-' + (currentQuestionNumber + 1);
};

const saveAnswer = function saveAnswer(currentQuestionNumber) {
    const answerName = ANSWER_PREFIX + currentQuestionNumber;

    let answerElements = document.getElementsByName(answerName);

    answerElements.forEach(function (element) {
    if (element.checked) {
      let saved = JSON.parse(localStorage.getItem(ANSWER_KEY));
      saved[answerName] = element.value;

      localStorage.setItem(ANSWER_KEY, JSON.stringify(saved));
    }
    });
};

const hasAnswer = function hasAnswer(currentQuestionNumber) {
    const answerName = ANSWER_PREFIX + currentQuestionNumber;
    const questions  = JSON.parse(localStorage.getItem(QUESTION_KEY));

    console.log(questions, questions[currentQuestionNumber]);

    if (questions.hasOwnProperty(currentQuestionNumber) && questions[currentQuestionNumber].hasOwnProperty('optional') && questions[currentQuestionNumber]['optional'] === true) {
        return true;
    }

    let answerElements = document.getElementsByName(answerName), result = false;

    for (let i in answerElements) {
        if (answerElements.hasOwnProperty(i) && (answerElements[i].checked || answerElements[i].value.length > 1)) {
            return true;
        }
    }

    return false;
};

const calculateResult = function calculateResult() {
  let savedAnswers = JSON.parse(localStorage.getItem(ANSWER_KEY)),
      savedQuestions = JSON.parse(localStorage.getItem(QUESTION_KEY)),
      sum = 0;

  for (let answer in savedAnswers) {
    if (savedAnswers.hasOwnProperty(answer) && !isNaN(parseInt(savedAnswers[answer]))) {
      let split = answer.split('-');
      sum += parseInt(savedAnswers[answer]) * parseInt(savedQuestions[parseInt(split[(split.length - 1)])].weight);
    }
  }

  return sum;
};

const calculateMaxPossible = function calculateMaxPossible() {
    const questions = JSON.parse(localStorage.getItem(QUESTION_KEY));
    let maxPossible = 0;

    for (let i = 0, l = questions.length; i < l; i++) {
        if (questions.hasOwnProperty(i)) {
            maxPossible += parseInt(questions[i].weight) * parseInt(questions[i].answers[(questions[i].answers.length - 1)].value);
        }
    }

    return maxPossible;
};

const calculateLowestPossible = function calculateLowestPossible() {
    const questions = JSON.parse(localStorage.getItem(QUESTION_KEY));
    let lowestPossible = 0;

    for (let i = 0, l = questions.length; i < l; i++) {
        if (questions.hasOwnProperty(i)) {
            lowestPossible += parseInt(questions[i].weight) * parseInt(questions[i].answers[0].value);
        }
    }

    // well this might be 0 in many cases but you could choose a lowest value > 0 so we have to do this
    return lowestPossible;
};

const stripHTML = function stripHTML(value) {
    let stripped = document.createElement('div');
    stripped.innerHTML = value;
    return stripped.textContent;
};

(function() {
    localStorage.setItem(FROM_KEY, '');

    let query = window.location.search;
    if (query.match(/from/)) {
        const params = new Map(query.slice(1).split('&').map(value => value.split('=')));

        if (params.has('from')) {
            let from = params.get('from');
            localStorage.setItem(FROM_KEY, stripHTML(from).substr(0, 10));
        }
    }

    localStorage.setItem(ANSWER_KEY, JSON.stringify({}));

    // get questions
    const promiseQ = getQuestions();
    // get result answers
    const promiseR = getResult();

    let start            = document.getElementById('start'),
        back             = document.getElementById('back'),
        forward          = document.getElementById('forward'),
        send             = document.getElementById('send'),
        saverBox         = document.getElementById('js-saver-box'),
        titleElement     = document.getElementById('js-title'),
        introElement     = document.getElementById('js-introduction'),
        questionElements = document.getElementById('js-question-list'),
        answerText       = document.getElementById('js-answer'),
        errorElement     = document.getElementById('js-error'),
        readmore         = document.getElementById('js-read-more')
    ;

  if (readmore) {
      readmore.addEventListener('click', function (e) {
          e.preventDefault();

          e.target.className = 'hidden';

          let targets = document.getElementsByClassName('read-more');

          if (targets) {
              for (let i = 0, l = targets.length; i < l; i++) {
                  if (targets.hasOwnProperty(i)) {
                      targets[i].className = 'read-more';
                  }
              }
          }
      });
  }

  let lastQuestionNumber,
      currentQuestionNumber = 0,
      totalQuestionCount;

  if (start) {
    start.addEventListener('click', function (e) {
        start.parentElement.className = 'control start hidden';
        start.parentElement.setAttribute('aria-hidden', 'true');
        forward.parentElement.className = 'control forward';
        forward.parentElement.setAttribute('aria-hidden', 'false');
        send.parentElement.className = 'control send hidden';
        send.parentElement.setAttribute('aria-hidden', 'true');

        if (introElement) {
            introElement.className = 'hidden';
        }

        const questions = JSON.parse(localStorage.getItem('questions')),
            first = questions[currentQuestionNumber];

        updateSaverBox(saverBox, questions, currentQuestionNumber);
        updateQuestionTitle(titleElement, first);
        updateAnswers(questionElements, first, currentQuestionNumber);

        let keys = Object.keys(questions);

        totalQuestionCount = keys.length;
        lastQuestionNumber = (keys.length - 1);
    });
  }

  if (forward) {
    forward.addEventListener('click', function (e) {
      if (errorElement) {
        errorElement.className = 'error-text hidden';
        errorElement.setAttribute('aria-hidden', 'true');
      }

      if (!hasAnswer(currentQuestionNumber)) {
        if (errorElement) {
          errorElement.className = 'error-text';
          errorElement.setAttribute('aria-hidden', 'false');
          errorElement.textContent = 'Please select an answer!'
        }

        setTimeout(function () {
          errorElement.className = 'error-text hidden';
            errorElement.setAttribute('aria-hidden', 'true');
        }, 3000);

        return;
      }

      saveAnswer(currentQuestionNumber);

      currentQuestionNumber++;

      if (currentQuestionNumber === 0) {
        back.parentElement.className = 'control send hidden';
        back.parentElement.setAttribute('aria-hidden', 'true');
      } else if (currentQuestionNumber < lastQuestionNumber) {
        start.parentElement.className = 'control start hidden';
        start.parentElement.setAttribute('aria-hidden', 'true');
        back.parentElement.className = 'control back';
        back.parentElement.setAttribute('aria-hidden', 'false');
        send.parentElement.className = 'control send hidden';
        send.parentElement.setAttribute('aria-hidden', 'true');
        forward.parentElement.className = 'control forward';
        forward.parentElement.setAttribute('aria-hidden', 'false');
      } else if (currentQuestionNumber >= lastQuestionNumber) {
        start.parentElement.className = 'control start hidden';
        start.parentElement.setAttribute('aria-hidden', 'true');
        back.parentElement.className = 'control back';
        back.parentElement.setAttribute('aria-hidden', 'false');
        send.parentElement.className = 'control send';
        send.parentElement.setAttribute('aria-hidden', 'false');
        forward.parentElement.className = 'control forward hidden';
        forward.parentElement.setAttribute('aria-hidden', 'true');
      }

      if (currentQuestionNumber > lastQuestionNumber) {
        currentQuestionNumber--;
      }

      const questions = JSON.parse(localStorage.getItem('questions')),
            question = questions[currentQuestionNumber];

      updateSaverBox(saverBox, questions, currentQuestionNumber);
      updateQuestionTitle(titleElement, question);
      updateAnswers(questionElements, question, currentQuestionNumber);
    });
  }

  if (back) {
    back.addEventListener('click', function (e) {
      if (errorElement) {
        errorElement.className = 'error-text hidden';
        errorElement.setAttribute('aria-hidden', 'true');
      }

      saveAnswer(currentQuestionNumber);

      currentQuestionNumber--;

      if (currentQuestionNumber <= 0) {
        back.parentElement.className = 'control send hidden';
        back.parentElement.setAttribute('aria-hidden', 'true');
      } else if (currentQuestionNumber < lastQuestionNumber) {
        start.parentElement.className = 'control start hidden';
        start.parentElement.setAttribute('aria-hidden', 'true');
        back.parentElement.className = 'control back';
        back.parentElement.setAttribute('aria-hidden', 'false');
        send.parentElement.className = 'control send hidden';
        send.parentElement.setAttribute('aria-hidden', 'true');
        forward.parentElement.className = 'control forward';
      } else if (currentQuestionNumber >= lastQuestionNumber) {
        start.parentElement.className = 'control start hidden';
        start.parentElement.setAttribute('aria-hidden', 'true');
        back.parentElement.className = 'control back';
        back.parentElement.setAttribute('aria-hidden', 'false');
        send.parentElement.className = 'control send';
        send.parentElement.setAttribute('aria-hidden', 'false');
        forward.parentElement.className = 'control forward hidden';
        forward.parentElement.setAttribute('aria-hidden', 'true');
      }

      if (currentQuestionNumber < 0) {
        currentQuestionNumber++;
      }

      const questions = JSON.parse(localStorage.getItem('questions')),
        question = questions[currentQuestionNumber];

      updateSaverBox(saverBox, questions, currentQuestionNumber);
      updateQuestionTitle(titleElement, question);
      updateAnswers(questionElements, question, currentQuestionNumber);
    });
  }

  if (send) {
    send.addEventListener('click', function (e) {
      if (errorElement) {
        errorElement.className = 'error-text hidden';
        errorElement.setAttribute('aria-hidden', 'true');
      }

      if (!hasAnswer(currentQuestionNumber)) {
        if (errorElement) {
          errorElement.className = 'error-text';
          errorElement.textContent = 'Please select an answer!'
          errorElement.setAttribute('aria-hidden', 'false');
        }

        setTimeout(function () {
          errorElement.className = 'error-text hidden';
          errorElement.setAttribute('aria-hidden', 'true');
        }, 3000);

        return;
      }

      saveAnswer(currentQuestionNumber);

      const calculated  = calculateResult();
      const maxPossible = calculateMaxPossible();

      const result = Math.round(Math.abs(calculated / maxPossible) * 100);

      const resultAnswers = JSON.parse(localStorage.getItem(RESULT_KEY));

      let answer;

      resultAnswers.answers.forEach(function (value) {
          if (value.threshold_min <= result && value.threshold_max >= result) {
            answer = value.text;
            return answer;
          }
      });

      localStorage.setItem(CALC_RESULT_KEY, JSON.stringify({answer: answer, result: result}));

      answerText.className = 'answer-text';
      answerText.prepend(answer);

      saverBox.textContent = 'The Answer';
      titleElement.textContent = 'Thank you for answering the questions!';
      questionElements.className = 'question-list hidden';
      start.parentElement.className = 'control start hidden';
      start.parentElement.setAttribute('aria-hidden', 'true');
      back.parentElement.className = 'control back hidden';
      back.parentElement.setAttribute('aria-hidden', 'true');
      send.parentElement.className = 'control send hidden';
      send.parentElement.setAttribute('aria-hidden', 'true');
      forward.parentElement.className = 'control forward hidden';
      forward.parentElement.setAttribute('aria-hidden', 'true');

      if (calculated >= 0 && result >= 0) {
        sendAnswers();
      }
    });
  }

  const sendTest = document.getElementById('js-send-test');
  if (sendTest) {
    sendTest.addEventListener('click', function (e) {
      sendAnswers();
    });
  }


  // @todo
  // 3. calculate result and present it (show if possibly interested or not)
  // 4. send result to owner if active
}());
