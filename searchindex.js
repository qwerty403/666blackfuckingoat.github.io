// search index for WYSIWYG Web Builder
var database_length = 0;

function SearchPage(url, title, keywords, description)
{
   this.url = url;
   this.title = title;
   this.keywords = keywords;
   this.description = description;
   return this;
}

function SearchDatabase()
{
   database_length = 0;
   this[database_length++] = new SearchPage("index.html", "Untitled Page", "untitled page double click to edit ", "");
   this[database_length++] = new SearchPage("page2.html", "Untitled Page", "untitled page double click to edit sign up for new account full name user password confirm mail nbsp Поиск Блеать ", "");
   return this;
}
