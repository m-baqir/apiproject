# apiproject
This app works by utilizing two APIs the trakt API and the TMDB movie API.
The main page to run is the List.php page.
I am also using the milligram Classless CSS package just to give it a different look.
The data flow works in this way:
- I get a list of top 10 movies from TRAKT
- I grab the movie IDs for those movies from TRAKT and use them to Access movie reviews for them from TMDB
- I also have a function to grab movie details from TMDB for movies. I am only using this for poster path information.

This is my integration of two APIs. I know it is alot of text on one page (the review for each movie could be a link to another page) and I know my code quality could be improved. i.e. have classes and methods. These are things that are on my TODO list.
