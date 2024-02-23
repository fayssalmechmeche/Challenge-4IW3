function openUserCreateModal() {
  fetch(`/admin/user/new`)
    .then((response) => response.text())
    .then((html) => {
      console.log(html);
      document.getElementById("content").innerHTML = html;
      displayModalTwo(true);
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération du formulaire:", error)
    );
}

function openUserEditModal(id) {
  fetch(`/admin/user/edit/${id}`)
    .then((response) => response.text())
    .then((html) => {
      document.getElementById("content").innerHTML = html;
      displayModalTwo(true);
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération du formulaire:", error)
    );
}

function openUserShowModal(id) {
  fetch(`/admin/user/show/${id}`)
    .then((response) => response.text())
    .then((html) => {
      document.getElementById("content").innerHTML = html;
      displayModalTwo(true);
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération du formulaire:", error)
    );
}

function openSocietyCreateModal() {
  fetch(`/admin/society/new`)
    .then((response) => response.text())
    .then((html) => {
      document.getElementById("content").innerHTML = html;
      displayModalTwo(true);
    })
    .catch((error) =>
      console.error("Erreur lors de la récupération du formulaire:", error)
    );
}

function displayModalTwo(isVisibleBoolean) {
  addClassToElementTwo();
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

function addClassToElementTwo() {
  //Ce script est propre à ce formulaire : il rajoute une class à la div id customer qui ne peut être gérée dynamiquement depuis le form builder. #}
  let customerDiv = document.getElementById("admin_user");
  if (customerDiv) {
    customerDiv.classList.add("flex", "flex-wrap", "gap-y-5", "gap-x-10");
  }
}

// added for the case we have one grid js and for the modal that use  displayModal
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

/// AJAX request USER ////

function addNewUser() {
  let form = document.getElementById("formNewUser");
  let formData = new FormData(form);
  let data = {};
  formData.forEach(function (value, key) {
    data[key] = value;
  });
  fetch("/admin/user/new", {
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
      refreshCardUser();

      loadGridUser();
    })
    .catch((error) => {
      console.error("Erreur:", error);
    });
}
function deleteUser(id, token) {
  if (confirm("Voulez-vous vraiment supprimer cette utlisateur ?")) {
    fetch("/admin/user/delete/" + id + "/" + token, {
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
        refreshCardUser();

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

  fetch("/admin/user/edit/" + id, {
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
      refreshCardUser();

      loadGridUser();
    })
    .catch((error) => {
      console.error("Erreur:", error);
    });
}

function refreshCardUser() {
  let cardCountUsers = document.getElementById("cardNumberLength");
  let cardCountUsersVerified = document.getElementById("cardUserVerfied");
  let cardCountUsersNotVerified = document.getElementById("cardUserNotVerfied");
  let isShow = document.getElementById("isShow") ? document.getElementById("isShow").dataset.isshow : 0;

  fetch(`/admin/user?isShow=${isShow}`, {
    method: "GET",
    headers: {
      "Content-Type": "application/json",
      "X-Requested-With": "XMLHttpRequest",
    },
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Erreur lors de la récupération des utilisateurs");
      }
      return response.json();
    })
    .then((data) => {
      cardCountUsers.innerHTML = data.data.countUsers;
      cardCountUsersVerified.innerHTML = data.data.countUsersVerified;
      cardCountUsersNotVerified.innerHTML = data.data.countUsersNotVerified;
    })
    .catch((error) => {
      console.error("Erreur:", error);
    });
}
