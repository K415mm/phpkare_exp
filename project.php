<?php
session_start();
require 'vendor/autoload.php';

use PHPKare\Microservices\Internal\UploaderService;
use PHPKare\Microservices\Internal\ValidatorService;
use PHPKare\Microservices\Internal\HandlerService;
use PHPKare\Microservices\Internal\FetcherService;
use PHPKare\Microservices\Internal\VisualizerService;

$uploader = new UploaderService();
$validator = new ValidatorService();
$handler = new HandlerService();
$fetcher = new FetcherService();
$visualizer = new VisualizerService();

$clientName = filter_input(INPUT_GET, 'client', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? "";
$projectName = filter_input(INPUT_GET, 'project', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? "";
$projectDate = filter_input(INPUT_GET, 'date', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? "";
$_SESSION['client'] = filter_input(INPUT_GET, 'client', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? "";
$_SESSION['project'] = filter_input(INPUT_GET, 'project', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? "";
$jsCode = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['nodesFile']) && isset($_FILES['connectionsFile'])) {
  try {
    $nodesFile = $uploader->upload($_FILES['nodesFile']);
    $connectionsFile = $uploader->upload($_FILES['connectionsFile']);

    $nodes = $handler->processCSV($nodesFile);
    $connections = $handler->processCSV($connectionsFile);

    if ($validator->validateNodes($nodes) && $validator->validateConnections($connections)) {
      // Generate JavaScript code to add nodes and connections
      $jsCode = "\n";
      foreach ($nodes as $node) {
        $jsCode .= "addNodeFromCSV(" . json_encode($node) . ");\n";
      }
      foreach ($connections as $connection) {
        $jsCode .= "addConnectionFromCSV(" . json_encode($connection) . ");\n";
      }
    } else {
      echo "<div class='alert alert-danger'>CSV files do not comply with the required structure.</div>";
    }
  } catch (Exception $e) {
    echo "<div class='alert alert-danger'>" . $e->getMessage() . "</div>";
  }
}


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
            <div class="row">
              <!-- Vertical Scrollbar -->
              <div class="col-md-6 col-sm-12">
                <div class="card overflow-hidden mb-6" style="height: 300px">
                  <h5 class="card-header">History</h5>
                  <div class="card-body ps ps--active-y" id="vertical-example">
                    <div class="history-panel">
                    
                      <div id="history" class="accordion">
                        <!-- History will be displayed here -->
                      </div>
                    </div>
                    <div class="ps__rail-x" style="left: 0px; bottom: -94px;">
                      <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                    </div>
                    <div class="ps__rail-y" style="top: 94px; height: 224px; right: 0px;">
                      <div class="ps__thumb-y" tabindex="0" style="top: 19px; height: 46px;"></div>
                    </div>
                  </div>
                </div>
              </div>
              <!--/ Vertical Scrollbar -->
              <div class="col-md-6 col-sm-12">
                <div class="card overflow-hidden mb-6" style="height: 300px">
                <div class="chat-history-header border-bottom">
                       
                         
                  <div class="row">
                    <div class="col-md-4 col-sm-4">
                      <h5>Project: <?php echo htmlspecialchars($projectName); ?></h5>
                    </div>
                    <div class="col-md-4 col-sm-4">
                      <h5>Client: <?php echo htmlspecialchars($clientName); ?></h5>
                    </div>
                    <div class="col-md-4 col-sm-4">
                      <h5>Date: <?php echo htmlspecialchars($projectDate); ?></h5>
                    </div>
                  </div>
                  
                         
                        
                       </div>




                  <div class="card-body ps ps--active-y" id="vertical-example2">
                    <div class="mb-3">
                      <label class="form-label">Node Type</label>
                      <select class="form-control" id="nodeType" name="nodeType" required>
                        <option value="workstation">Workstation</option>
                        <option value="router">Router</option>
                        <option value="switch">Switch</option>
                        <option value="firewall">Firewall</option>
                        <option value="database">Database</option>
                      </select>
                    </div>
                    <button type="button" class="btn btn-outline-primary" onclick="addNode()">Add Node</button>
                    <!-- Node Modal -->
                    <div class="modal fade" id="nodeModal" tabindex="-1" aria-labelledby="nodeModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content glass-effect">
                          <div class="modal-header">
                            <h5 class="modal-title" id="nodeModalLabel">Add Node</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <form id="nodeForm">
                              <div class="mb-3">
                                <label for="nodeName" class="form-label">Node Name</label>
                                <input type="text" class="form-control" id="nodeName" required>
                              </div>
                              <div class="mb-3">
                                <label for="nodeType" class="form-label">Node Type</label>
                                <select class="form-control" id="nodeType" required>
                                  <option value="workstation">Workstation</option>
                                  <option value="router">Router</option>
                                  <option value="switch">Switch</option>
                                  <option value="firewall">Firewall</option>
                                  <option value="database">Database</option>
                                </select>
                              </div>
                              <div class="mb-3">
                                <label for="manufacturer" class="form-label">Manufacturer</label>
                                <input type="text" class="form-control" id="manufacturer" required>
                              </div>
                              <div class="mb-3">
                                <label for="realName" class="form-label">Real Name</label>
                                <input type="text" class="form-control" id="realName" required>
                              </div>
                              <div class="mb-3">
                                <label for="os" class="form-label">OS</label>
                                <input type="text" class="form-control" id="os" required>
                              </div>
                              <div class="mb-3">
                                <label for="version" class="form-label">Version</label>
                                <input type="text" class="form-control" id="version" required>
                              </div>
                              <button type="button" class="btn btn-primary" onclick="saveNode()">Save Node</button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>

                    <button type="button" class="btn btn-outline-secondary" onclick="addConnection()">Add Connection</button>
                    <!-- Connection Modal -->
                    <div class="modal fade" id="connectionModal" tabindex="-1" aria-labelledby="connectionModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content glass-effect">
                          <div class="modal-header">
                            <h5 class="modal-title" id="connectionModalLabel">Add Connection</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <form id="connectionForm">
                              <div class="mb-3">
                                <label for="fromNode" class="form-label">From Node ID</label>
                                <input type="text" class="form-control" id="fromNode" required>
                              </div>
                              <div class="mb-3">
                                <label for="toNode" class="form-label">To Node ID</label>
                                <input type="text" class="form-control" id="toNode" required>
                              </div>
                              <div class="mb-3">
                                <label for="color" class="form-label">Connection Color</label>
                                <input type="text" class="form-control" id="color" required>
                              </div>
                              <div class="mb-3">
                                <label for="width" class="form-label">Connection Width</label>
                                <input type="text" class="form-control" id="width" required>
                              </div>
                              <button type="button" class="btn btn-primary" onclick="saveConnection()">Save Connection</button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>

                    <button type="button" class="btn btn-outline-success" onclick="fetchAll()">Fetch Vulnerabilities</button>


                    <form onsubmit="event.preventDefault(); submitForm();">
                      <div class="mb-3">
                        <label for="project" class="form-label">Project</label>
                        <input type="hidden" class="form-control" id="project" name="project" value="<?php echo $projectName; ?>" required>
                        <input type="hidden" class="form-control" id="client" name="client" value="<?php echo $clientName; ?>" required>
                      </div>
                      <button type="submit" class="btn btn-primary">Generate YAML</button>
                    </form>


                  </div>
                </div>
                <div class="mt-4">
                  <button class="btn btn-primary waves-effect waves-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasBoth" aria-controls="offcanvasBoth">
                    Upload Network Assets from CSV
                  </button>
                  <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasBoth" >
                    <div class="offcanvas-header">
                      <h5 id="offcanvasBothLabel" class="offcanvas-title">Upload Network Assets from CSV</h5>
                      <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body my-auto mx-0 flex-grow-0">
                      <form action="" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                          <label for="nodesFile" class="form-label">Nodes CSV File</label>
                          <input type="file" class="form-control" id="nodesFile" name="nodesFile" required>
                        </div>
                        <div class="mb-3">
                          <label for="connectionsFile" class="form-label">Connections CSV File</label>
                          <input type="file" class="form-control" id="connectionsFile" name="connectionsFile" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload CSV</button>
                      </form>
                      <button type="button" class="btn btn-label-secondary d-grid w-100 waves-effect" data-bs-dismiss="offcanvas">
                        Cancel
                      </button>
                    </div>
                  </div>
                </div>
                <div class="ps__rail-x" style="left: 0px; bottom: -94px;">
                  <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                </div>
                <div class="ps__rail-y" style="top: 94px; height: 224px; right: 0px;">
                  <div class="ps__thumb-y" tabindex="0" style="top: 19px; height: 46px;"></div>
                </div>
                <div class="ps__rail-x" style="left: 0px; bottom: -94px;">
                  <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div>
                </div>
                <div class="ps__rail-y" style="top: 94px; height: 224px; right: 0px;">
                  <div class="ps__thumb-y" tabindex="0" style="top: 19px; height: 46px;"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Horizontal Scrollbar -->

          <!--/ Horizontal Scrollbar -->

          <!-- Vertical & Horizontal Scrollbars -->
          <div class="col-12">

            <div class="card overflow-hidden" style="height: 500px" id="network">

              <h5 class="card-header">Vertical &amp; Horizontal Scrollbars</h5>
              <div class="card-body ps ps--active-x ps--active-y" id="both-scrollbars-example">




                <div class="ps__rail-x" style="width: 1080px; left: 0px; bottom: 0px;">
                  <div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 608px;"></div>
                </div>
                <div class="ps__rail-y" style="top: 0px; height: 424px; right: 0px;">
                  <div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 137px;"></div>
                </div>
              </div>
            </div>
          </div>
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