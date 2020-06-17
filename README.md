# Retrap - a simple app for managing recruiting messages
I had this idea... Letting recruiters answer some questions in advance, so they can know if writing you made any sense.
Right now after implementing a simple version of this idea I'm not even sure if people will use it.
It's pretty much the developer's approach on solving things: Just write another app.
So, I'll try to and see how it works out. At least it's good practise of getting a project done and keeping it simple.

## What's this name all about?
Retrap is an abbreviation for RecruiterTrap which has been derived from [Honeytrap/Honeypot](https://en.wikipedia.org/wiki/Honeypot_(computing) "Wikipedia article about honeypots in computing").
Repot/Recpot just sounded a bit much misleading.
The principle of honeytrapping arbitrary attackers via prepared worthwhile targets is well-known in IT security.
Although it sounds a bit harsh in regard to recruiters (well, at least to some of them) there can be some similarity.

## Why all this?
This app is supposed to be some way of filtering out incoming recruiting requests.
As probably many developers I found some preferred settings regarding work,
and it just wouldn't make sense connecting with companies or recruiters whose vision and guidelines differ in too many aspects.
Since with my previous projects I was bound to being very much browser backwards compatible I wanted to test some features without having to take care of older browsers.
Besides, after working mainly backend for years now, I wanted to get some more experience in creating UIs, again.

### Will people use it?
I don't know. But I want to find out. This is a tech-savvy approach so people might find this more time-consuming than I did.
At least this was a project where I could try a few things and get back to keeping things simple.
Also, training getting ideas into code and published seems to be worthwhile as well.

## KISS
Keeping it simple I tried to use only native javascript and a simple architecture.
So you won't find any REACT in here. As well as other frameworks or libraries even though I considered using modernizrjs.
The backend receiving the answers is just a simple script receiving and checking the frontend's data and writing it into a json file.
Therefore, no additional framework and libraries here as well.

## How To use it
1) At first rename the following files:
- `/index.dist.html` to `/index.html`
- `/data/config.dist.php` to `/data/config.php` 
- `/data/q/en/question.dist.json` to `/data/q/en/question.json`
2) Then adjust the contents of these files like you want:
- `/index.html`: adjust the link `https://YOURDOMAIN` pointing to your domain (or remove it)
- `/data/config.php`: adjust the parameters `email_address` to your email address and `send_mail` to `true` or `false` depending on if you want to get an email if there was a new entry.
- `/data/q/en/question.json`: adjust your questions and remove/add what you like to ask there
3) Upload to a webspace supporting php
