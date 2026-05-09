<?php

class HomeController extends Controller {
    public function index() {
        // Khoi tao model goi tour
        $packageModel = $this->model('PackageModel');

        // Cau hinh phan trang
        $toursPerPage = 6;
        $currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $offset = ($currentPage - 1) * $toursPerPage;

        // Lay du lieu tu model
        $packages = $packageModel->getFeaturedPackagesPaginated($toursPerPage, $offset);
        $totalTours = $packageModel->getTotalToursCount();
        $totalPages = ceil($totalTours / $toursPerPage);
        $locations = $packageModel->getDistinctLocations();

        // Chuan bi du lieu cho view
        $data = [
            'packages' => $packages,
            'locations' => $locations,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalTours' => $totalTours
        ];

        // Render view kem du lieu
        $this->view('home/index', $data);
    }
}
