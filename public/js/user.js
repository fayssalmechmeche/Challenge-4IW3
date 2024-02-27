function openUserCreateModal() {
  fetch(`/user/new`)
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

function openUserEditModal(id) {
  fetch(`/user/edit/${id}`)
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

function openUserShowModal(id) {
  fetch(`/user/show/${id}`)
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

function openSocietyCreateModal() {
  fetch(`/society/new`)
    .then((response) => response.text())
    .then((html) => {
      document.getElementById("content").innerHTML = html;
      displayModal(true);
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération du formulaire:", error)
    );
}

function displayModal(isVisibleBoolean) {
  addClassToElement();
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

/// AJAX request USER ////

function addNewUser() {
  let form = document.getElementById("formNewUser");
  let formData = new FormData(form);
  let data = {};
  formData.forEach(function (value, key) {
    data[key] = value;
  });
  fetch("/user/new", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
    body: JSON.stringify(data),
  })
    .then((response) => response.json())
    .then((responseData) => {
      addFlash(
        responseData.success ? "success" : "danger",
        responseData.message
      );

      loadGridUser();
    })
    .catch((error) => {
      console.error("Erreur:", error);
    });
}
function deleteUser(id, token) {
  if (confirm("Voulez-vous vraiment supprimer cette utlisateur ?")) {
    fetch("/user/delete/" + id + "/" + token, {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
    })
      .then((response) => response.json())
      .then((responseData) => {
        addFlash(
          responseData.success ? "success" : "danger",
          responseData.message
        );

        loadGridUser();
      })
      .catch((error) => {
        console.error("Erreur:", error);
      });
  }
}
function editUser(id) {
  let form = document.getElementById("formEditUser");
  let formData = new FormData(form);
  let data = {};
  formData.forEach(function (value, key) {
    data[key] = value;
  });

  fetch("/user/edit/" + id, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
    body: JSON.stringify(data),
  })
    .then((response) => response.json())
    .then((responseData) => {
      addFlash(
        responseData.success ? "success" : "danger",
        responseData.message
      );
      loadGridUser();
    })
    .catch((error) => {
      console.error("Erreur:", error);
    });
}

function addClassToElement() {
  //Ce script est propre à ce formulaire : il rajoute une class à la div id customer qui ne peut être gérée dynamiquement depuis le form builder. #}
  let admin_userDiv = document.getElementById("admin_user");
  if (admin_userDiv) {
    admin_userDiv.classList.add("flex", "flex-wrap", "gap-y-5", "gap-x-10");
  }
}
