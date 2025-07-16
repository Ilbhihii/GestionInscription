import { BrowserRouter, Routes, Route } from "react-router-dom";
import Accueil from "./components/Accueil";
import Formulaire from "./components/Formulaire";
import FormulaireM from "./components/FormulaireM";
import PaiementTableau from "./components/PaiementTableau";

function App() {
  return (
    <BrowserRouter>
      <Routes>
        <Route path="/" element={<Accueil />} />
        <Route path="/inscription" element={<Formulaire />} />
        <Route path="/paiement" element={<FormulaireM />} />
        <Route path="/Tableau_Paiement" element={<PaiementTableau />} />
      </Routes>
    </BrowserRouter>
  );
}

export default App;
