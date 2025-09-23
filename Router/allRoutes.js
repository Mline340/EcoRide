import Route from "./Route.js";

//Définir ici vos routes
export const allRoutes = [
    new Route("/", "Accueil", "/pages/home.html"),
    new Route("/covoit", "Covoiturages", "/pages/covoiturage/covoit.html"),
    new Route("/signin", "Connexion", "/pages/auth/signin.html"),
    new Route("/signup", "Inscription", "/pages/auth/signup.html", "/js/auth/signup.js"),
    new Route("/account", "Mon compte", "/pages/auth/account.html"),
    new Route("/historique", "Historique trajets", "/pages/covoiturage/historique.html"),
    new Route("/annonce", "Publier une annonce", "/pages/covoiturage/annonce.html"),

];

//Le titre s'affiche comme ceci : Route.titre - websitename
export const websiteName = "EcoRide";