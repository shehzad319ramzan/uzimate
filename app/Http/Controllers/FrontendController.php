<?php

namespace App\Http\Controllers;

class FrontendController extends Controller
{
    private $_dir = null;

    public function __construct()
    {
        $this->_dir = "frontend.";
    }

    public function welcome()
    {
        return view('frontend.home');
    }

    public function about()
    {
        return view('frontend.about');
    }

    public function contact()
    {
        return view('frontend.contact');
    }

    public function retailSector($sector)
    {
        // Map sector slugs to readable names
        $sectors = [
            'beauty-personal-care' => 'Beauty & Personal Care',
            'food-hospitality' => 'Food & Hospitality',
            'healthcare-providers' => 'Healthcare Providers',
            'recreation-leisure' => 'Recreation & Leisure',
            'fitness-wellness' => 'Fitness & Wellness',
            'wholesale-suppliers' => 'Wholesale & Suppliers',
            'bakeries-confectioneries' => 'Bakeries & Confectioneries',
            'cafes-dessert-parlors' => 'Cafes & Dessert Parlors',
            'retail-outlets' => 'Retail Outlets',
            'automotive-services' => 'Automotive Services',
            'fashion-jewelry' => 'Fashion & Jewelry',
            'takeaway-food-outlet' => 'Takeaway Food Outlet',
        ];

        $sectorName = $sectors[$sector] ?? 'Retail Sector';
        
        return view('frontend.retail-sector', compact('sector', 'sectorName'));
    }
}
