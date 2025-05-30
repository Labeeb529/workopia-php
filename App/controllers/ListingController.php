<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;

class ListingController
{
    protected $db;

    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    public function index()
    {

        $listings = $this->db->query('SELECT * FROM listings;')->fetchAll();

        loadView('listings/index', [
            'listings' => $listings
        ]);
    }

    public function create()
    {
        loadView('listings/create');
    }

    /**
     * Show a single Listing
     * 
     * @param array $params
     * @return void
     */

    public function show($params)
    {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        //check if listing exists

        if (!$listing) {
            ErrorController::notFound("Listing not found");
            return;
        }

        loadView('listings/show', [
            'listing' => $listing
        ]);
    }

    /**
     * Store data in database
     * 
     * @return void
     */

    public function store()
    {
        $allowedFields = ['title', 'description', 'salary', 'requirements', 'benefits', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email'];

        $newListingData = array_intersect_key($_POST, array_flip($allowedFields));

        $newListingData['user_id'] = 1;

        $newListingData = array_map('sanitize', $newListingData);

        $requiredFields = ['title', 'description', 'salary', 'email', 'city', 'state'];

        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';


            }
        }
        if (!empty($errors)) {
            // Reload view with errors
            loadView('listings/create', [
                'errors' => $errors,
                'listing' => $newListingData
            ]);
        } else {
            // Submit data
            $fields = [];

            foreach ($newListingData as $field => $value) {
                $fields[] = $field;
            }

            $fields = implode(', ', $fields);

            $values = [];

            foreach ($newListingData as $field => $value) {
                if ($value === '') {
                    $newListingData[$field] = null;
                }
                $values[] = ':' . $field;

            }
            $values = implode(', ', $values);


            $query = "INSERT INTO listings ({$fields}) VALUES ({$values})";

            $this->db->query($query, $newListingData);

            redirect('/listings');
        }


    }

    /**
     * Delete a Listing
     * 
     * @param array $params
     * @return void
     * 
     */

    public function destroy($params)
    {
        $id = $params['id'];

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }

        $this->db->query('DELETE FROM listings WHERE id = :id', $params);

        // Set flash message 

        $_SESSION['success_message'] = "Listing deleted successfully.";

        redirect('/listings');
    }

    /**
     * Show a listing edit form Listing
     * 
     * @param array $params
     * @return void
     */

    public function edit($params)
    {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        //check if listing exists

        if (!$listing) {
            ErrorController::notFound("Listing not found");
            return;
        }


        loadView('listings/edit', [
            'listing' => $listing
        ]);

    }

    /**
     * Update a listing
     * 
     * @param array $params
     * @return void
     */

    public function update($params)
    {
        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        //check if listing exists

        if (!$listing) {
            ErrorController::notFound("Listing not found");
            return;
        }

        $allowedFields = ['title', 'description', 'salary', 'requirements', 'benefits', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email'];

        $updateValues = [];

        $updateValues = array_intersect_key($_POST, array_flip($allowedFields));

        $updateValues = array_map('sanitize', $updateValues);

        $requiredFields = ['title', 'description', 'email', 'city', 'state'];
        
        $errors = [];

        foreach($requiredFields as $field){
            if(empty($updateValues[$field]) || !Validation::string($updateValues[$field])){
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }

        if(!empty($errors)){
            loadView('listings/edit', [
                'listing' => $listing,
                'errors' => $errors
            ]);
        }else{
            //Submit to database
            $updateFields = [];

            foreach(array_keys($updateValues) as $field){
                $updateFields[] = $field . ' = :' . $field; 
            }
            $updateFields = implode(', ', $updateFields);

            $updateValues['id'] = $id;
            $updateQuery = "UPDATE listings SET $updateFields WHERE id = :id";

            $this->db->query($updateQuery, $updateValues);

            $_SESSION['success_message'] = 'Listing Updated.';

            redirect('/listings/' . $id);
            
            // inspectAndDie($updateQuery);

        }

    }


}