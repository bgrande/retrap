# 0.1.0
+ initial page
+ start questions
+ present questions
+ decide on: Do we want to be compatible down to IE11? (e.g. having polyfills and using let/const)
    -> newer browsers it is
+ make x questions work
+ make back and forward work
+ save answers to localStorage when forward/back was pressed
+ restore stored answers when using back/forward
+ let send button appear on last question (or all answered)
+ let send button send the answered questions to the backend

# 0.2.0
+ calculate max possible and lowest possible results (including weighting)
+ make threshold a percentage (of max)
+ correctly calculate the response
+ after send was clicked we get a thank you and result page
+ write simple backend saving the payload into json file
+ extend backend to send an email on successful send with link to result page and if the threshold was met
+ radiobox styling: https://www.w3schools.com/howto/howto_css_custom_checkbox.asp | https://jsfiddle.net/8uycrv12/

# 0.2.1
+ fix vertical positioning of question box (auto center!)
+ fix button positioning on mobile

# 0.3.0
+ add animations/transitions -> do not use display: none for hidden
+ add description and title
+ improve for accessibility
+ font color contrast is too low (on mobile)
+ prevent horizontal scrolling on mobile
+ create tile icons
+ create icons
+ add manifest
+ add browserconfig
+ create favicon
+ buttons hover + active
+ shorten intro text (or only show on expand)
+ extend readme

# 0.3.1
+ improve contrast
+ improve symbols

# 0.4.0
+ github
+ license
+ show/fork on github
+ log anonymized IP to recognize duplicates

# 0.5.0
- allow multiple answers, so we can get name and email
- use github repo here as well
- combine back/forward/last/first logic
- make sure we catch the name and email of the person questioned in the end if result was interesting (another backend script) (or with the last question as input fields)
    - get contact data of questionee
    - make sure we catch the name and email of the person questioned in the end if result was interesting (another backend script)
    - from parameter from whitelist (backend!) or only allowing a certain string length? / encode/remove html chars
- spam protection (block write requests from same address for a few (random) minutes)

# 0.6.0
- some unit tests
- introduce translations
- csrf protection (although not necessary)?

# 1.0.0
- recognize state after reload and continue where the user left
- navigation history (#anchors)
