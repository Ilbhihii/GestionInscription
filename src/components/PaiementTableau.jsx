import React, { useState, useEffect } from "react";
import axios from "axios";

function PaiementTableau() {
  const [classe, setClasse] = useState("");
  const [mois, setMois] = useState("");
  const [eleves, setEleves] = useState([]);
  const [eleveSelectionne, setEleveSelectionne] = useState("");
  const [tableau, setTableau] = useState([]);

  useEffect(() => {
    if (classe) {
      axios
        .get(`http://localhost/backend/get_eleves_by_classe.php?classe=${classe}`)
        .then((res) => {
          setEleves(res.data);
          setEleveSelectionne("");
        })
        .catch((err) => {
          console.error(err);
          setEleves([]);
        });
    }
  }, [classe]);

  const handleAjouter = () => {
    const eleve = eleves.find(e => e.code_massar === eleveSelectionne);
    if (!eleve || !mois) return;

    const montant = parseFloat(eleve.prix_mensuel) + (eleve.transport ? parseFloat(eleve.prix_transport) : 0);

    setTableau(prev => [
      ...prev,
      {
        code_massar: eleve.code_massar,
        nom: eleve.nom,
        prenom: eleve.prenom,
        mois,
        prix_mensuel: eleve.prix_mensuel,
        transport: eleve.transport ? eleve.prix_transport : 0,
        montant
      }
    ]);
  };

  const handleModifier = (index) => {
    const newMois = prompt("Nouveau mois :", tableau[index].mois);
    if (newMois) {
      const copie = [...tableau];
      copie[index].mois = newMois;
      setTableau(copie);
    }
  };

  return (
    <div className="container mt-4">
      <h4 className="mb-3">Gestion des paiements</h4>
      <div className="row mb-3">
        <div className="col-md-3">
          <label>Classe</label>
          <select className="form-select" value={classe} onChange={handleCodeChange} required>
                          <option value="">-- Sélectionner un élève --</option>
                          {eleves.map((el) => (
                            <option key={el.classe} value={el.classe}>
                              {el.classe}
                            </option>
              ))}
          </select>
        </div>

        {classe && (
          <>
            <div className="col-md-3">
              <label>Mois</label>
              <select className="form-select" value={mois} onChange={(e) => setMois(e.target.value)}>
                <option value="">-- Choisir un mois --</option>
                {["Septembre", "Octobre", "Novembre", "Décembre", "Janvier", "Février", "Mars", "Avril", "Mai", "Juin"].map((m) => (
                  <option key={m} value={m}>{m}</option>
                ))}
              </select>
            </div>

            <div className="col-md-3">
              <label>Élève</label>
              <select className="form-select" value={eleveSelectionne} onChange={(e) => setEleveSelectionne(e.target.value)}>
                <option value="">-- Choisir un élève --</option>
                {eleves.map((el) => (
                  <option key={el.code_massar} value={el.code_massar}>
                    {el.nom} {el.prenom}
                  </option>
                ))}
              </select>
            </div>

            <div className="col-md-3 d-flex align-items-end">
              <button className="btn btn-success w-100" onClick={handleAjouter}>Ajouter au tableau</button>
            </div>
          </>
        )}
      </div>

      {tableau.length > 0 && (
        <table className="table table-bordered mt-4">
          <thead className="table-light">
            <tr>
              <th>Nom</th>
              <th>Prénom</th>
              <th>Mois</th>
              <th>Mensuel (DH)</th>
              <th>Transport (DH)</th>
              <th>Total (DH)</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            {tableau.map((row, i) => (
              <tr key={i}>
                <td>{row.nom}</td>
                <td>{row.prenom}</td>
                <td>{row.mois}</td>
                <td>{row.prix_mensuel}</td>
                <td>{row.transport}</td>
                <td>{row.montant}</td>
                <td>
                  <button className="btn btn-warning btn-sm" onClick={() => handleModifier(i)}>Modifier Mois</button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      )}
    </div>
  );
}

export default PaiementTableau;
