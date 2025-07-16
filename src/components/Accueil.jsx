import React from "react";
import { Link } from "react-router-dom";

function Accueil() {
  return (
    <div className="container mt-5 text-center">
      <h2 className="mb-4">Bienvenue dans le système de gestion</h2>
      <div className="d-flex justify-content-center gap-4">
        <Link to="/inscription" className="btn btn-outline-primary btn-lg">
          Inscription
        </Link>
        <Link to="/paiement" className="btn btn-outline-success btn-lg">
          Paiement Mensuel
        </Link>
        <Link to="/Tableau_Paiement" className="btn btn-outline-info btn-lg">
          Tableau de Paiement
        </Link>
      </div>
    </div>
  );
}

export default Accueil;
