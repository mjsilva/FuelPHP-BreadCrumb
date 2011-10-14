#FuelPHP BreadCrumb

## Description

Allows you to build BreadCrumb links easily in FuelPHP.


Keep in mind this may not be the usual breadcrumb you are looking for. 
This will not create a map parent->child from where you have been instead, this will track your movements in the site and build the crumbs from there.

Ex:

<pre>
1)
User Action: http://example.com/bo/dashboard
BreadCrumb: \Breadcrumb::add_crumb("Dashboard , "bo/dashboard", true);
BreadCrumb Links: Dashboard
2)
User Action: http://example.com/bo/users/set
BreadCrumb: \Breadcrumb::add_crumb("Set User", "bo/users/set");
BreadCrumb Links:  Dashboard / Set User
3)
User Action:  http://example.com/bo/users/view/12
BreadCrumb: \Breadcrumb::add_crumb("View User 12", "bo/view/view/12");
BreadCrumb Links: Dashboard / Set User 12 / View User 12
4)
User Action:  http://example.com/bo/system/configs
BreadCrumb: \Breadcrumb::add_crumb("System Configs", "bo/system/configs");
BreadCrumb Links:  Dashboard / Set User 12 /  View User 12 / System Configs
etc....
If the user decides to go back to for Set User the breadcrumb will "rewind" and be like this:
BreadCrumb Links: Dashboard / Set User
</pre>


##Development Team

* Manuel Joao Silva - Lead Developer ([http://manueljoaosilva.com](http://manueljoaosilva.com))

##License

FuelPHP-BreadCrumb is released under the MIT License.

## Installation
* Copy it to your app/classes

## Usage

<pre>

\Breadcrumb::add_crumb( string [title] , string [link], bool [is home]);

 +-----------------------------------------------------------------------------------+
 + Title   | The title of the Crumb it's the anchor text. This got to be unique.     +
 +-----------------------------------------------------------------------------------+
 + Link    | The Link of the current page, if not provided we'll use \Uri::current() +
 +-----------------------------------------------------------------------------------+
 + is_home | If it's set to true crumbs will be reset and this will be the first.    +
 +-----------------------------------------------------------------------------------+
</pre>