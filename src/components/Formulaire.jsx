import React, { useState } from "react";
import axios from "axios";
import { Link } from "react-router-dom";
import { eleves } from "../service/Data";

function Formulaire() {
  const [formData, setFormData] = useState({
    code_massar: "",
    nom: "",
    prenom: "",
    classe: "",
    parent_nom: "",
    parent_prenom: "",
    prix_inscription: "",
    prix_mensuel: "",
    date_paiement: "",
    transport: false,
    prix_transport: 0
  });

  const [ajoutFait, setAjoutFait] = useState(false);

  // Lorsqu'on sélectionne un élève depuis le select
  const handleCodeMassarChange = (e) => {
    const selectedCode = e.target.value;
    const selectedEleve = eleves.find(el => el.code_massar === selectedCode);

    if (selectedEleve) {
      setFormData({
        ...formData,
        code_massar: selectedEleve.code_massar,
        nom: selectedEleve.nom,
        prenom: selectedEleve.prenom,
        classe: selectedEleve.classe
      });
    } else {
      setFormData({
        ...formData,
        code_massar: selectedCode,
        nom: "",
        prenom: "",
        classe: ""
      });
    }
  };

  // Gestion du changement pour les autres champs
  const handleChange = (e) => {
    const { name, value, type, checked } = e.target;
    setFormData({
      ...formData,
      [name]: type === "checkbox" ? checked : value
    });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await axios.post("http://localhost/backend/addClient.php", formData);
      alert("Élève ajouté avec succès !");
      setAjoutFait(true);
    } catch (err) {
      alert("Erreur lors de l'ajout.");
    }
  };

  return (
    <div className="container mt-5">
      <div className="card shadow">
        <div className="card-header bg-primary text-white text-center fw-bold">
          Formulaire d’inscription élève
        </div>
        <div className="card-body">
          <form onSubmit={handleSubmit}>
            <div className="row">
              {/* Code Massar avec select */}
              <div className="col-md-6 mb-3">
                <label className="form-label">Code Massar</label>
                <select
                  className="form-select"
                  value={formData.code_massar}
                  onChange={handleCodeMassarChange}
                  required
                >
                  <option value="">-- Sélectionner un élève --</option>
                  {eleves.map((el) => (
                    <option key={el.code_massar} value={el.code_massar}>
                      {el.code_massar} - {el.nom} {el.prenom}
                    </option>
                  ))}
                </select>
              </div>

              {/* Nom */}
              <div className="col-md-6 mb-3">
                <label className="form-label">Nom</label>
                <input
                  name="nom"
                  className="form-control"
                  value={formData.nom}
                  onChange={handleChange}
                  required
                />
              </div>

              {/* Prénom */}
              <div className="col-md-6 mb-3">
                <label className="form-label">Prénom</label>
                <input
                  name="prenom"
                  className="form-control"
                  value={formData.prenom}
                  onChange={handleChange}
                  required
                />
              </div>

              {/* Classe */}
              <div className="col-md-6 mb-3">
                <label className="form-label">Classe</label>
                <input
                  name="classe"
                  className="form-control"
                  value={formData.classe}
                  onChange={handleChange}
                  required
                />
              </div>

              {/* Infos parent */}
              <div className="col-md-6 mb-3">
                <label className="form-label">Nom du parent</label>
                <input
                  name="parent_nom"
                  className="form-control"
                  onChange={handleChange}
                  required
                />
              </div>
              <div className="col-md-6 mb-3">
                <label className="form-label">Prénom du parent</label>
                <input
                  name="parent_prenom"
                  className="form-control"
                  onChange={handleChange}
                  required
                />
              </div>

              {/* Prix inscription + mensuel + date */}
              <div className="col-md-4 mb-3">
                <label className="form-label">Prix d’inscription (DH)</label>
                <input
                  name="prix_inscription"
                  type="number"
                  className="form-control"
                  onChange={handleChange}
                  required
                />
              </div>
              <div className="col-md-4 mb-3">
                <label className="form-label">Prix mensuel (DH)</label>
                <input
                  name="prix_mensuel"
                  type="number"
                  className="form-control"
                  onChange={handleChange}
                  required
                />
              </div>
              <div className="col-md-4 mb-3">
                <label className="form-label">Date de paiement</label>
                <input
                  name="date_paiement"
                  type="date"
                  className="form-control"
                  onChange={handleChange}
                  required
                />
              </div>

              {/* Transport */}
              <div className="col-md-4 mb-3">
                <div className="form-check mt-4">
                  <input
                    type="checkbox"
                    className="form-check-input"
                    name="transport"
                    onChange={handleChange}
                    id="transportCheck"
                  />
                  <label className="form-check-label" htmlFor="transportCheck">
                    Transport
                  </label>
                </div>
              </div>

              <div className="col-md-8 mb-3">
                <label className="form-label">Prix transport (DH)</label>
                <input
                  name="prix_transport"
                  type="number"
                  className="form-control"
                  onChange={handleChange}
                  disabled={!formData.transport}
                  required={formData.transport}
                />
              </div>
            </div>

            {/* Boutons */}
            <div className="d-flex justify-content-center gap-3 mt-4">
              <button type="submit" className="btn btn-success">
                Ajouter l’élève
              </button>

              {ajoutFait && (
                <a
                  className="btn btn-primary"
                  href={`http://localhost/backend/exportExcel.php?code_massar=${formData.code_massar}`}
                  target="_blank"
                  rel="noopener noreferrer"
                >
                  Imprimer Reçu
                </a>
              )}

              <Link to="/" className="btn btn-secondary">
                ← Retour
              </Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}

export default Formulaire;
