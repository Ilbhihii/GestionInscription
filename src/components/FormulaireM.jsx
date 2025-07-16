import React, { useState } from 'react';
import axios from 'axios';
import { Link } from 'react-router-dom';
import { eleves } from '../service/Data';

function PaiementMensuelForm() {
  const [codeMassar, setCodeMassar] = useState('');
  const [eleve, setEleve] = useState(null);
  const [mois, setMois] = useState('');
  const [montant, setMontant] = useState('');

  const handleCodeChange = async (e) => {
    const value = e.target.value;
    setCodeMassar(value);

    if (value.length >= 5) {
      try {
        const res = await axios.get(`http://localhost/backend/get_eleve.php?code_massar=${value}`);
        setEleve(res.data);
        const total = parseFloat(res.data.prix_mensuel) + (res.data.transport ? parseFloat(res.data.prix_transport) : 0);
        setMontant(total);
      } catch (err) {
        setEleve(null);
        alert("Élève introuvable !");
      }
    }
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      await axios.post("http://localhost/backend/ajouter_paiement.php", {
        code_massar: codeMassar,
        mois,
        montant,
      });
      alert("Paiement enregistré !");
    } catch (err) {
      alert("Erreur lors de l'enregistrement.");
    }
  };

  return (
    <div className="container mt-5">
      <div className="card shadow">
        <div className="card-header bg-success text-white text-center fw-bold">
          Paiement Mensuel
        </div>
        <div className="card-body">
          <form onSubmit={handleSubmit}>
            <div className="mb-3">
              <label className="form-label">Code Massar</label>
              <select className="form-select" value={codeMassar} onChange={handleCodeChange} required >
                <option value="">-- Sélectionner un élève --</option>
                {eleves.map((el) => (<option key={el.code_massar} value={el.code_massar}>
                    {el.code_massar} - {el.nom} {el.prenom}
                    </option>
                ))}
                </select>
            </div>

            {eleve && (
              <>
                <div className="row">
                  <div className="col-md-4 mb-3">
                    <label className="form-label">Nom</label>
                    <input className="form-control" value={eleve.nom} disabled />
                  </div>
                  <div className="col-md-4 mb-3">
                    <label className="form-label">Prénom</label>
                    <input className="form-control" value={eleve.prenom} disabled />
                  </div>
                  <div className="col-md-4 mb-3">
                    <label className="form-label">Classe</label>
                    <input className="form-control" value={eleve.classe} disabled />
                  </div>
                </div>

                <div className="mb-3">
                  <label className="form-label">Mois</label>
                  <select className="form-select" value={mois} onChange={(e) => setMois(e.target.value)} required>
                    <option value="">Choisir un mois</option>
                    {["Septembre", "Octobre", "Novembre", "Décembre", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin"].map((m) => (
                      <option key={m} value={m}>{m}</option>
                    ))}
                  </select>
                </div>

                <div className="mb-3">
                  <label className="form-label">Montant total (DH)</label>
                  <input
                    type="number"
                    className="form-control"
                    value={montant}
                    onChange={(e) => setMontant(e.target.value)}
                    required
                  />
                </div>

                <div className="d-flex gap-3">
                  <button type="submit" className="btn btn-success">Enregistrer Paiement</button>
                  <a
                    href={`http://localhost/backend/export.php?code_massar=${codeMassar}`}
                    className="btn btn-primary"
                    target="_blank"
                    rel="noopener noreferrer"
                  >
                    Imprimer Reçu
                  </a>
                </div>
              </>
            )}

            <div className="mt-4">
              <Link to="/" className="btn btn-secondary">← Retour</Link>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}

export default PaiementMensuelForm;
