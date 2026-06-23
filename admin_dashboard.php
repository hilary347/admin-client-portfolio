<?php
session_start();
if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
    header('Location: admin_login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Admin Panel</title>
      <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
      <link rel="stylesheet" href="admin_dashboard.css">
  </head>
  <body>
    <nav class="navbar navbar-dark bg-dark fixed-top">
      <div class="container-fluid">
        <button class="hamburger d-md-none" onclick="toggleSidebar()">☰</button>
        <span class="navbar-brand"><b>Light's</b> Dashboard</span>
        <button onclick="logout()" class="btn btn-danger btn-sm">Logout</button>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">

        <!-- Sidebar -->
        <div class="col-md-3 sidebar d-none d-md-block">
          <div class="nav-links">
            <button class="nav-btn active-btn" onclick="showTab('projectsTab')">
              Manage Projects
            </button>
            <button class="nav-btn" onclick="showTab('bioTab')">
              Edit Bio
            </button>
            <button class="nav-btn" onclick="showTab('inboxTab')">Inbox</button>
          </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 offset-md-3 col-12 main-content">
          <!-- Project Management Section -->
          <div id="projectsTab" class="admin-content">

            <section class="content-box">
              <h3>Add New Project</h3>

              <div class="form-box">
                <input type="text" id="pName" placeholder="Project Name" />
                <input type="text" id="pDesc" placeholder="Description" />
                <input type="text" id="pPill" placeholder="Pill (e.g. Web, App, Design)" />
                <div class="tag-inputs">
                  <input type="text" id="pTag1" placeholder="Tag 1" />
                  <input type="text" id="pTag2" placeholder="Tag 2" />
                  <input type="text" id="pTag3" placeholder="Tag 3" />
                </div>
                <label for="pImg" class="form-label">Project Image</label>
                <input type="file" id="pImg" accept="image/*" class="form-control mb-2" />
                <div id="projectImagePreview" class="mt-2"></div>
                <button class="save-btn" onclick="saveProject()" id="projectSaveButton">
                  Save Project
                </button>
                <button class="cancel-btn" id="cancelProjectEditButton" onclick="cancelProjectEdit()" style="display:none; margin-left: 8px;">
                  Cancel Edit
                </button>
              </div>
            </section>

            
            <section class="content-box">
              <h3>Existing Projects</h3>

              <table>
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Pill</th>
                    <th>Tags</th>
                    <th>Action</th>
                  </tr>
                </thead>

                <tbody>
                  <tr>
                    <td>Portfolio Website</td>
                    <td>Web</td>
                    <td>UI, Frontend</td>
                    <td class="action-cell">
                      <div class="action-buttons">
                        <button class="view-btn">View</button>
                      </div>
                    </td>
                  </tr>
                  <tr>
                    <td>E-Commerce App</td>
                    <td>App</td>
                    <td>UX, Mobile</td>
                    <td class="action-cell">
                      <div class="action-buttons">
                        <button class="view-btn">View</button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </section>
          </div>

          <!-- Bio Section -->
          <div id="bioTab" class="admin-content" style="display: none">
            <section class="content-box">
              <h3>Update Bio</h3>
              <div class="form-box mb-3">
                <label for="bioImage" class="form-label">Profile / Bio Image</label>
                <input
                  type="file"
                  id="bioImage"
                  accept="image/*"
                  class="form-control mb-2"
                />
                <div id="bioImagePreview" class="bio-image-preview mt-2"></div>
              </div>
              <textarea
                id="bioInput"
                class="form-control mb-2"
                rows="5"
              ></textarea>
              <button onclick="saveBio()" class="save-btn">
                Update About Me
              </button>
            </section>
          </div>

          <!-- Inbox Section -->
          <div id="inboxTab" class="admin-content" style="display: none">
            <section class="content-box">
              <h3>Messages</h3>
              <div id="messageList"></div>
            </section>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script>
      function logout() {
        window.location.href = "admin_login.php?logout=1";
      }


      function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        sidebar.classList.toggle('show');
      }

      
      function showTab(tabId) {
        document
          .querySelectorAll(".admin-content")
          .forEach((div) => (div.style.display = "none"));
        document.getElementById(tabId).style.display = "block";

        
        document
          .querySelectorAll(".nav-btn")
          .forEach((btn) => btn.classList.remove("active-btn"));
        event.target.classList.add("active-btn");

        
        document.querySelector('.sidebar').classList.remove('show');
      }

      
      function readFileInputAsDataURL(inputElement) {
        return new Promise((resolve) => {
          const file = inputElement.files[0];
          if (!file) {
            resolve("");
            return;
          }
          const reader = new FileReader();
          reader.onload = () => resolve(reader.result);
          reader.onerror = () => resolve("");
          reader.readAsDataURL(file);
        });
      }

      function updatePreview(previewId, imageUrl) {
        const previewEl = document.getElementById(previewId);
        if (imageUrl) {
          previewEl.innerHTML = `<img src="${imageUrl}" alt="Preview" class="img-fluid rounded" style="max-height: 200px;" />`;
        } else {
          previewEl.innerHTML = "";
        }
      }

      let editingProjectIndex = null;
      let editingProjectId = null;
      let currentProjectImage = "";

      async function saveBio() {
        const imageData = await readFileInputAsDataURL(
          document.getElementById("bioImage")
        );
        localStorage.setItem(
          "userBio",
          document.getElementById("bioInput").value
        );
        if (imageData) {
          localStorage.setItem("bioImage", imageData);
        }
        updatePreview("bioImagePreview", localStorage.getItem("bioImage"));
        alert("Bio Updated!");
      }
      
      function updateBioImagePreview() {
        const storedImage = localStorage.getItem("bioImage");
        updatePreview("bioImagePreview", storedImage);
      }

      document.getElementById("bioInput").value =
        localStorage.getItem("userBio") || "";
      document.getElementById("bioImage").addEventListener(
        "change",
        async () => {
          const imageData = await readFileInputAsDataURL(
            document.getElementById("bioImage")
          );
          updatePreview("bioImagePreview", imageData);
        }
      );
      updateBioImagePreview();
      document
        .getElementById("pImg")
        .addEventListener("change", async () => {
          const imageData = await readFileInputAsDataURL(
            document.getElementById("pImg")
          );
          updatePreview("projectImagePreview", imageData);
        });

      function getProjectFormValues() {
        return {
          name: document.getElementById("pName").value,
          desc: document.getElementById("pDesc").value,
          pill: document.getElementById("pPill").value,
          tags: [
            document.getElementById("pTag1").value,
            document.getElementById("pTag2").value,
            document.getElementById("pTag3").value,
          ].filter(Boolean),
        };
      }

      function populateProjectForm(project, index) {
        document.getElementById("pName").value = project.name;
        document.getElementById("pDesc").value = project.desc;
        document.getElementById("pPill").value = project.pill || "";
        document.getElementById("pTag1").value = project.tags[0] || "";
        document.getElementById("pTag2").value = project.tags[1] || "";
        document.getElementById("pTag3").value = project.tags[2] || "";
        currentProjectImage = project.image || "";
        updatePreview("projectImagePreview", currentProjectImage);
        editingProjectIndex = index;
        editingProjectId = project.id || null;
        document.getElementById("projectSaveButton").textContent = "Update Project";
        document.getElementById("cancelProjectEditButton").style.display = "inline-block";
      }

      function resetProjectForm() {
        document.getElementById("pName").value = "";
        document.getElementById("pDesc").value = "";
        document.getElementById("pPill").value = "";
        document.getElementById("pTag1").value = "";
        document.getElementById("pTag2").value = "";
        document.getElementById("pTag3").value = "";
        document.getElementById("pImg").value = "";
        currentProjectImage = "";
        updatePreview("projectImagePreview", "");
        editingProjectIndex = null;
        document.getElementById("projectSaveButton").textContent = "Save Project";
        document.getElementById("cancelProjectEditButton").style.display = "none";
      }

      function cancelProjectEdit() {
        resetProjectForm();
      }

      function saveProject() {
        alert("Project section is hardcoded. Add/edit is disabled.");
        resetProjectForm();
      }

      
      function loadMessages() {
        const msgs = JSON.parse(localStorage.getItem("messages")) || [];
        const container = document.getElementById("messageList");
        container.innerHTML = msgs.length ? "" : "No messages yet.";
        msgs.forEach((m) => {
          container.innerHTML += `
                <div class="card mb-2 p-2">
                    <strong>From: ${m.name} (${m.email})</strong>
                    <p>${m.msg}</p>
                </div>`;
        });
      }

      
      loadMessages();

      
     window.addEventListener('DOMContentLoaded', () => {

    const sidebar = document.querySelector('.sidebar');

    setTimeout(() => {
        sidebar.classList.add('loaded');
    }, 100);

});
    </script>
   
  </body>
</html>
