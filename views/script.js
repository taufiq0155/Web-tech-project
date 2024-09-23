function validateEmail() {
  const email = document.getElementById("email").value;
  const emailError = document.getElementById("emailError");
  const regex = /^[a-zA-Z0-9._%+-]+@(gmail\.com|yahoo\.com)$/;

  if (!regex.test(email)) {
    emailError.textContent =
      "Please enter a valid email (only gmail.com or yahoo.com)";
    return false;
  }
  emailError.textContent = "";
  return true;
}

function validateForm() {
  if (!validateEmail()) {
    return false;
  }
  return true;
}
function updateButtonText() {
  const roleSelect = document.getElementById("role");
  const submitButton = document.getElementById("submitBtn");
  const roleError = document.getElementById("roleError");
  const specialistContainer = document.getElementById("specialistContainer");
  const specialistInput = document.getElementById("specialist");

  if (roleSelect.value === "") {
    roleError.textContent = "Please select a role.";
    submitButton.disabled = true;
    specialistContainer.style.display = "none"; // Hide the specialization field
  } else {
    roleError.textContent = ""; // Clear error
    submitButton.disabled = false;
    submitButton.value = roleSelect.value === "doctor" ? "Apply" : "Register";

    // Show or hide the specialization field based on selected role
    if (roleSelect.value === "doctor") {
      specialistContainer.style.display = "block"; // Show specialization field
    } else {
      specialistContainer.style.display = "none"; // Hide specialization field
      specialistInput.value = ""; // Clear specialization input if not a doctor
    }
  }
}

function validateForm() {
  const roleSelect = document.getElementById("role");
  const roleError = document.getElementById("roleError");
  const specialistContainer = document.getElementById("specialistContainer");
  const specialistInput = document.getElementById("specialist");
  let isValid = true;

  if (roleSelect.value === "") {
    roleError.textContent = "Please select a role.";
    isValid = false;
  } else {
    roleError.textContent = ""; // Clear error
  }

  if (roleSelect.value === "doctor" && specialistInput.value.trim() === "") {
    document.getElementById("err8").textContent =
      "Please enter your specialization.";
    isValid = false;
  }

  return isValid;
}

// Disable submit button on page load until a role is selected
window.onload = function () {
  const submitButton = document.getElementById("submitBtn");
  const roleSelect = document.getElementById("role");
  submitButton.disabled = roleSelect.value === ""; // Disable if no role is selected
};
