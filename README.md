# PHP Examples

## cards.php
So there are a few examples of the card sorter I have built.

### A bit of back information on this.
So there are many times when some sort of collation is needed. Sports cards, trading cards, etc. They are in mixed lots and I need a way to quick log the information per card and qty, build a fast html table or cvs data spread for excel. That is where this evolved from, a need because notepad was not cutting the mustard.

The previous versions before 2.5 were simplistic and a bit wonky.<br>
I just didn't have enough time to invest into this simple app before.

I also would not recommend using anything previous to 2.8 the newest evolution of this page.<br>
I was and have been running it local machine VPN only, so I wont be breaking anything.<br>
But there is no data cleansing or validation on the earlier versions.<br>

The true upgrade with 2.8 there were some major<br>
rewrites and changes to the whole script/app.
- cardStack class; contained card stack object, automatic.
- superVariable class; self handling variable fitting with forms.
- User entry data cleaning, verification, error checking, default values.
- A more simple way to output all the forms of data (table, cvs, missing, raw tags, forms!)
- The front end UI was changed drastically, container fit sections.
- Much of the app self fills references to form updates, calls, input entry.

There will be a new version upcoming soon.
- Javascript download/save (blind div entry for text document saving to local pc)
- Possibly error/warning alerts (under controls) when data type is entered incorrectly.
- I always wanted to make a tag building class, able to fill pages, tables, divs, etc..
