import axios from "axios";

const API_BASE = "http://localhost/backend";

export const addEleve = async (eleve) => {
  try {
    const res = await axios.post(`${API_BASE}/addClient.php`, eleve);
    return res.data;
  } catch (error) {
    console.error("Erreur lors de l'ajout :", error);
    throw error;
  }
};

export const exportRecu = (id) => {
  window.location.href = `${API_BASE}/exportExcel.php?id=${id}`;
};
