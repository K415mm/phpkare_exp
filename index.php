<?php
session_start();
require 'vendor/autoload.php';



?>
<?php
include "Templates/head.php"
?>

<?php
include "Templates/menu.php"
?>

      <!-- / Navbar -->

      <!-- Layout container -->
      <div class="layout-page">
        <!-- Content wrapper -->
        <div class="content-wrapper">
          <!-- Menu -->
          <aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu bg-menu-theme flex-grow-0">
            <div class="container-xxl d-flex h-100">
              <ul class="menu-inner py-1">
                <!-- Page -->
                <li class="menu-item active">
                  <a href="index.html" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-smart-home"></i>
                    <div data-i18n="Page 1">Page 1</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="page-2.html" class="menu-link">
                    <i class="menu-icon tf-icons ti ti-app-window"></i>
                    <div data-i18n="Page 2">Page 2</div>
                  </a>
                </li>
              </ul>
            </div>
          </aside>
          <!-- / Menu -->

          <!-- Content -->
          <div class="container-xxl flex-grow-1 container-p-y">
          <div class="container text-center mt-5">
        <h1>Welcome to the Network Project Manager</h1>
        <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addProjectModal">Add Project</button>
    </div>

    <!-- Add Project Modal -->
    <div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProjectModalLabel">Add New Project</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="projectForm">
                        <div class="mb-3">
                            <label for="clientName" class="form-label">Client Name</label>
                            <input type="text" class="form-control" id="clientName" required>
                        </div>
                        <div class="mb-3">
                            <label for="projectName" class="form-label">Project Name</label>
                            <input type="text" class="form-control" id="projectName" required>
                        </div>
                        <div class="mb-3">
                            <label for="projectDate" class="form-label">Project Date</label>
                            <input type="date" class="form-control" id="projectDate" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Project</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="asset/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('projectForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const clientName = document.getElementById('clientName').value;
            const projectName = document.getElementById('projectName').value;
            const projectDate = document.getElementById('projectDate').value;

            // Redirect to the project page with the project details
            window.location.href = `project.php?client=${clientName}&project=${projectName}&date=${projectDate}`;
        });
    </script>
          </div>

          <!-- Horizontal Scrollbar -->

          <!--/ Horizontal Scrollbar -->

          <!-- Vertical & Horizontal Scrollbars -->
     
          <!--/ Vertical & Horizontal Scrollbars -->
        </div>
      </div>
      <!-- kais clean here end -->

      <!--/ Content -->

      <!-- Footer -->
      <?php
include "Templates/foot.php"
?>
      <!-- / Footer -->

      <div class="content-backdrop fade"></div>
    </div>
    <!--/ Content wrapper -->
  </div>

  <!--/ Layout container -->
  </div>
  </div>

  <!-- Overlay -->
  <div class="layout-overlay layout-menu-toggle"></div>

  <!-- Drag Target Area To SlideIn Menu On Small Screens -->
  <div class="drag-target"></div>

  <!--/ Layout wrapper -->

  <!-- Core JS -->
  <!-- build:js assets/vendor/js/core.js -->

  <script src="assets/vendor/libs/jquery/jquery.js"></script>
  <script src="assets/vendor/libs/popper/popper.js"></script>
  <script src="assets/vendor/js/bootstrap.js"></script>
  <script src="assets/vendor/libs/node-waves/node-waves.js"></script>
  <script src="assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="assets/vendor/libs/hammer/hammer.js"></script>

  <script src="assets/vendor/js/menu.js"></script>
  <script src="https://unpkg.com/vis-network/standalone/umd/vis-network.min.js"></script>
  <!-- endbuild -->

  <!-- Vendors JS -->
  <!-- Vendors JS -->


  <!-- Main JS -->


  <!-- Page JS -->
  <script src="assets/js/extended-ui-perfect-scrollbar.js"></script>
  <!-- Main JS -->

  <script src="assets/js/main.js"></script>

  <!-- Page JS -->
  <div id="js-data" data-js-code='<?php echo json_encode($jsCode); ?>'></div>
    <!-- Analysis Modal -->
    <div class="modal fade" id="analysisModal" tabindex="-1" aria-labelledby="analysisModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="analysisModalLabel">Analyzing Nodes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="analysisStatus">Fetching vulnerabilities...</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>