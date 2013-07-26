RESTful-
========

RESTful API to view, edit, delete, add and search products.

Database has a table of name products.
CREATE TABLE `products` 
(
`id` int(11) NOT NULL AUTO_INCREMENT,
`name` varchar(50) NOT NULL,
`category` varchar(50) NOT NULL,
`description` varchar(100) NOT NULL,
`brand` varchar(50) NOT NULL,
`price` int(11) NOT NULL,
PRIMARY KEY (`id`)
) AUTO_INCREMENT=1 ;


For testing the API, download Advanced REST client Application for Google Chrome
https://chrome.google.com/webstore/detail/advanced-rest-client/hgmloofddffdnphfgcellkdfbfbjeloo

or for Mozilla Firefox
https://addons.mozilla.org/en-us/firefox/addon/restclient/

// 1.) Retrieve the list of products
GET http://localhost/restapi/listproducts

//2.) View Product
GET http://localhost/restapi/product?id=2

//3.) Search
GET http://localhost/restapi/searchproduct?name=shir

//4.) Create a product
POST http://localhost/restapi/createproduct?name=Watch

//5.) Update a product
PUT http://localhost/restapi/updateproduct?id=3&name=cellphone

//6.) Delete a product
DELETE http://localhost/restapi/deleteproduct?id=8
