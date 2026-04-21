let postModal = document.getElementById("post-modal");
let closeModalButton = document.getElementById("close-button");
let postModalDescription = document.getElementById("post-modal-description");
let postList = document.getElementById("post");
let createPostButton = document.getElementById("create-post-button");
let descriptionError = document.getElementById("descError");
let postModalTitle = document.getElementById("post-modal-title");
let postForm = document.getElementById("postForm");
let postOption = document.getElementById("post-option");
let postMenuPanel = document.getElementById("post-menu-panel");
let editPostBtn = document.getElementById("edit-post-btn");

function openModalForEdit() {
    postModalTitle.textContent = "Edit Post";
    createPostButton.textContent = "Save";
    postModal.style.display = "block";
    document.body.classList.add("modal-open");
    console.log("Edit post with ID:", id, "and text:", text);
}

function closeModal() {
    postModal.style.display = "none";
    document.body.classList.remove("modal-open");
    postModalDescription.value = "";
    descriptionError.textContent = "";
    postModalDescription.style.borderColor = "";
}

closeModalButton.onclick = function () {
    closeModal();
};

postOption.onclick = function (e) {
    postMenuPanel.style.display = postMenuPanel.style.display === "block" ? "none" : "block";
};

function formHasErrors() {
    descriptionError.textContent = "";
    if (postModalDescription.value.trim() === "") {
        postModalDescription.style.borderColor = "red";
        descriptionError.textContent = "Please enter something for your post.";
        return true;
    }
    postModalDescription.style.borderColor = "";
    return false;
}

postForm.onsubmit = function (e) {
    if (formHasErrors()) {
        e.preventDefault();
    }
};