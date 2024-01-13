// function updateProgress(id, percentage) {
//     const progressCircle = document.getElementById("progressCircle" + id);
//     const progressArrow = document.getElementById("progressArrow" + id);
//     const radius = progressCircle.r.baseVal.value; // Obtenez le rayon du cercle de progression
//     const circumference = 2 * Math.PI * radius; // Calculez la circonférence basée sur le rayon
//     const offset = circumference - (percentage / 100) * circumference;
//     progressCircle.style.strokeDashoffset = offset;
//     progressArrow.style.transform = `rotate(${percentage * 3.6}deg)`; // 360 degrés pour 100%
// }

// // Test de la fonction pour chaque barre de progression avec des pourcentages différents
// updateProgress(1, 15);
// updateProgress(2, 15);
// updateProgress(3, 35);
// updateProgress(4, 35);
