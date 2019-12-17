This is a URL monitor. You store URLs in a MySql database and it tests them periodically to get their http error code. If a URL turns red, it has returned an error. If it is flashing red, it has returned more than 3 errors inside 120 seconds, indicating that the websie is probably offline.

Urls can be placed into categories and each category has it's own page on the website.

Installation
1. Copy the contents of the "urlmonitor" folder to your web root (or a subfolder of localhost).
2. Import the "urlmonitor.sql" file into a new MySql database.
3. Edit "framework/config.php" to add your database details and your base url for Urlmonitor.
4. It should now be up and running.

Testing
There is a page called "Test Urls" where you can see a selection of http code test in action.
You can test a http code directly by adding a query string like so: ?code=404

To see a traffic light used in more than one place, see the "Poltics", "Sport" or "Technology & Science" pages which all test a BBC url that is also on the "BBC Sites" page.