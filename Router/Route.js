export default class Route {
    constructor(url, title, pathHtml, authorize, pathJS = "") {
      this.url = url;
      this.title = title;
      this.pathHtml = pathHtml;
      this.pathJS = pathJS;
      this.authorize = authorize;
    }
}

/*
[]-> tout le monde peut y accéder 
["disconnected"] -> réserver aux utilisateurs déconnecté 
["Utilisateur"] -> réserver aux utilisateurs avec le rôle utilisateur 
[admin]-> Réserver aux utilisateurs avec le rôle admin 
["employé"]-> Réserver aux utilisateur avec le rôle employé 

*/