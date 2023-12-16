function openSocietyCreateModal() {
  fetch(`/admin/society/new`)
    .then((response) => response.text())
    .then((html) => {
      console.log(html);
      document.getElementById("content").innerHTML = html;
      displayModal(true);
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération du formulaire:", error)
    );
}

function openSocietyEditModal(id) {
  fetch(`/admin/society/edit/${id}`)
    .then((response) => response.text())
    .then((html) => {
      console.log(html);
      document.getElementById("content").innerHTML = html;
      // document.getElementById(
      //   "formEditSociety"
      // ).action = `/admin/society/edit/${id}`;
      displayModal(true);
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération du formulaire:", error)
    );
}

function openSocietyShowModal(id) {
  fetch(`/admin/society/show/${id}`)
    .then((response) => response.text())
    .then((html) => {
      console.log(html);
      document.getElementById("content").innerHTML = html;
      displayModal(true);
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération du formulaire:", error)
    );
}

function openUserCreateModal() {
  fetch(`/admin/user/new`)
    .then((response) => response.text())
    .then((html) => {
      console.log(html);
      document.getElementById("content").innerHTML = html;
      displayModal(true);
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération du formulaire:", error)
    );
}

function displayModal(isVisibleBoolean) {
  if (isVisibleBoolean) {
    document.getElementById("modal_content_id").style.display = "block";
    document.getElementById("modal_id").style.display = "block";
    setTimeout(() => {
      document.getElementById("modal_content_id").style.top = "50%";
    }, 20);
  } else {
    document.getElementById("modal_content_id").style.top = "-500px";
    setTimeout(() => {
      document.getElementById("modal_content_id").style.display = "none";
      document.getElementById("modal_id").style.display = "none";
    }, 200);
  }
}