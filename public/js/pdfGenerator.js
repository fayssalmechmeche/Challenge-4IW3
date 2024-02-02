document.addEventListener("DOMContentLoaded", function () {
  let btn = document.querySelector("#dl-pdf");
  btn.addEventListener("click", generatePDF);
});

function generatePDF() {
  html2canvas(document.querySelector(".Generator"), {
    scale: 1.1, // Ajustez ce paramètre pour obtenir le meilleur résultat
    onclone: function (clonedDoc) {
      const clonedElement = clonedDoc.querySelector(".Generator");
      // Assurez-vous que l'élément cloné n'a pas de marge supérieure ni de bordure supérieure
      clonedElement.style.marginTop = "0";
      clonedElement.style.borderTop = "none";

      let devis = clonedElement.querySelector(".devis");
      devis.style.marginTop = "0";
      devis.style.paddingTop = "0";
      let gridTh = clonedElement.querySelectorAll(".gridjs-th");
      gridTh.forEach((th) => {
        th.style.marginTop = "0";
        th.style.paddingTop = "0";
        th.style.height = "30px";
      });

      let gridThContent = clonedElement.querySelectorAll(".gridjs-th-content");
      gridThContent.forEach((th) => {
        th.style.marginTop = "0";
        th.style.paddingTop = "0";
        th.style.height = "30px";
      });

      let gridTd = clonedElement.querySelectorAll(".gridjs-td");
      gridTd.forEach((td) => {
        td.style.marginTop = "0";
        td.style.paddingTop = "0";
        td.style.marginBottom = "10px";
        td.style.height = "30px";
      });

      let totalTableH3 = clonedElement.querySelectorAll(
        ".total-sub-container h3"
      );
      totalTableH3.forEach((h3) => {
        h3.style.marginTop = "0";
        h3.style.paddingTop = "0";
        h3.style.marginBottom = "14px";
      });

      let totalTableP = clonedElement.querySelectorAll(".total-tab p");

      totalTableP.forEach((total) => {
        total.style.marginTop = "0";
        total.style.paddingTop = "0";
        total.style.marginBottom = "14px";
        total.style.paddingBottom = "28px";
        total.style.height = "28px";
      });
    },
  }).then(function (canvas) {
    const imgData = canvas.toDataURL("image/png");
    const pdf = new jspdf.jsPDF({
      orientation: "portrait",
      unit: "mm",
      format: "a4",
    });
    const pdfWidth = pdf.internal.pageSize.getWidth();
    const pdfHeight = pdf.internal.pageSize.getHeight();
    const imgWidth = canvas.width;
    const imgHeight = canvas.height;
    const ratio = pdfWidth / imgWidth;
    // Définir les marges
    const marginTop = 10; // marge en haut en mm
    const marginBottom = 10; // marge en bas en mm
    const usableHeight = pdfHeight - marginTop - marginBottom;
    let heightLeft = imgHeight;
    let position = 0;
    let canvasCopy = null;
    let copyContext = null;
    // Créer une copie du canvas pour chaque nouvelle page
    while (heightLeft >= 0) {
      // Créer un nouveau canvas
      canvasCopy = document.createElement("canvas");
      canvasCopy.width = imgWidth;
      canvasCopy.height = Math.min(usableHeight / ratio, heightLeft);
      copyContext = canvasCopy.getContext("2d");
      // Copier la section appropriée de l'image originale
      copyContext.drawImage(
        canvas,
        0,
        position,
        imgWidth,
        Math.min(usableHeight / ratio, heightLeft),
        0,
        0,
        imgWidth,
        Math.min(usableHeight / ratio, heightLeft)
      );
      // Ajouter la section au PDF avec des marges
      pdf.addImage(
        canvasCopy.toDataURL("image/png"),
        "PNG",
        0,
        marginTop,
        pdfWidth,
        canvasCopy.height * ratio,
        "",
        "FAST"
      );
      heightLeft -= usableHeight / ratio;
      position += usableHeight / ratio;
      if (heightLeft > 0) {
        pdf.addPage();
      }
    }
    // Générer un horodatage pour le nom du fichier
    const timestamp = new Date().toISOString().replace(/[\W_]+/g, "");
    pdf.save(`devis_${timestamp}.pdf`);


    // Ajouter le canvas à l'élément de prévisualisation dans le document HTML
    // const canvasPreview = document.getElementById("canvasPreview");
    // canvasPreview.appendChild(canvas);
  });
}
