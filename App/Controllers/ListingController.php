<?php

namespace App\Controllers;
use Framework\Database;
use Framework\Session;
use Framework\Validation;
use Framework\Authorization;

class ListingController{

    protected $db;

    public function __construct(){
        //die('HomeController');
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }

    public function index(){
        //inspectAndDie(Validation::email('john@example.com'));
        //inspectAndDie(Validation::match('zayyan','zayyan'));

        $listings = $this->db->query('SELECT * FROM workopia.listings ORDER BY created_at DESC')->fetchAll();
        loadView('listings/index', [
            'listings' => $listings
        ]);
    }

    public function create(){
        loadView('listings/create');
    }

    /**
     * Display a post
     * @param array $params
     * @return void
     */
    public function show($params) {
        $id = $params['id'] ?? '';
        //inspect($id); 

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM workopia.listings WHERE id = :id',$params)->fetch();
        
        if(!$listing){
            ErrorController::notFound('Listing Not Found');
            return;
        }
        //inspect($listing);

        loadView('listings/show', [ 'listing' => $listing ] );

    }

    /**
     * Store data in DB
     * @param mixed $data
     * @return void
     */
    public function store(){

        $allowedFields = ['title','description','salary','tags','company','address',
        'city','state','phone','email','requirements','benefits'];

        $newListingData = array_intersect_key($_POST,array_flip($allowedFields));

        $newListingData['user_id'] = Session::get('user')['id'];

        $newListingData = array_map('sanitize', $newListingData);

        $requiredFields = ['title', 'description', 'email', 'city', 'state'];

        $errors = [];

        foreach($requiredFields as $field){
            if(empty($newListingData[$field]) && !Validation::string($newListingData[$field])){
                $errors[$field] = ucfirst($field) . 'is required';
            }
        }

        if(!empty($errors)){
            //Reload the view
            loadView('listings/create', [
                "errors" => $errors,
                "listing" => $newListingData
            ]);
        } else{
            // Submit the data
            //$query = 'INSERT INTO workopia.listings 
            //(user_id, title, description, salary, tags, company, address, city, 
            //state, phone, email, requirements, benefits) 
            //VALUES 
            //(:user_id, :title, :description, :salary, :tags, :company, :address, :city,
            //:state, :phone, :email, :requirements, :benefits)';

            //$this->db->query($query, $newListingData);

            $fields = [];

            foreach($newListingData as $field => $value){
                $fields[] = $field;
            }

            $fields = implode(',',$fields);

            //inspectAndDie($fields);

            $values = [];

            foreach($newListingData as $field => $value){
                if( $value==='' ){
                    $newListingData[$field] = null;
                }
                $values[] = ':' . $field;
            }

            $values = implode(',',$values);

            $query = "INSERT INTO workopia.listings ({$fields}) VALUES ({$values})";
            
            $this->db->query($query, $newListingData);

            Session::setFlashMessage('success_message','Listing created successfully.');

            redirect('/listings');
        }
    }


    /**
     * Delete a post
     * @param array $params
     * @return void
     */
    public function destroy($params){
        $id = $params['id'];
        //inspect($id);

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM workopia.listings WHERE id = :id', $params)->fetch();


        if(!$listing){
            ErrorController::notFound('Listing Not Found');
            return;
        }

        //Authorization
        if(!Authorization::isOwner($listing->user_id)){
            //$_SESSION['error_message'] = "You are not authorized to delete this listing";
            $current_user_id = Session::get('user')['id'];
            Session::setFlashMessage('error_message','You are not authorized to delete this listing.');
            return redirect("/listings/{$listing->id}");
        }

        $result = $this->db->query('DELETE FROM workopia.listings WHERE id = :id', $params)->execute();

        if($result){
            //$_SESSION['success_message'] = 'Listing deleted successfully';
            Session::setFlashMessage('success_message','Listing deleted successfully');
            redirect('/listings');
        }else{
            echo "Failed to delete the record.";
        }
    }

    /**
     * Show the edit listing form
     * @param array $params
     * @return void
     */
    public function edit($params) {

        $id = $params['id'] ?? '';
        //inspect($id); 

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM workopia.listings WHERE id = :id',$params)->fetch();
        
        if(!$listing){
            ErrorController::notFound('Listing Not Found');
            return;
        }

        //Authorization
        if(!Authorization::isOwner($listing->user_id)){
            $current_user_id = Session::get('user')['id'];
            Session::setFlashMessage('error_message','You are not authorized to update this listing.');
            return redirect("/listings/{$listing->id}");
        }
        


        //inspect($listing);

        loadView('listings/edit', [ 'listing' => $listing ] );

    }


    /**
     * Update a listing
     * 
     * @param array $params
     * @return void 
     */
    public function update($params){
        //inspectAndDie($params);

        $id = $params['id'] ?? '';

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM workopia.listings WHERE id = :id',$params)->fetch();
        
        if(!$listing){
            ErrorController::notFound('Listing Not Found');
            return;
        }

        //Authorization
        if(!Authorization::isOwner($listing->user_id)){
            $current_user_id = Session::get('user')['id'];
            Session::setFlashMessage('error_message','You are not authorized to update this listing.');
            return redirect("/listings/{$listing->id}");
        }
        

        $allowedFields = ['title','description','salary','tags','company','address',
        'city','state','phone','email','requirements','benefits'];

        $updateValues = [];

        $updateValues = array_intersect_key($_POST,array_flip($allowedFields));

        $updateValues = array_map('sanitize', $updateValues);

        $requiredFields = ['title','description','salary','email','city','state'];

        $errors = [];

        foreach($requiredFields as $field){
            if(empty($updateValues[$field]) || !Validation::string($updateValues[$field])){
                $errors[$field] = ucfirst($field) . ' is required.';
            }
        }

        //inspect($errors);

        if(!empty($errors)){
            loadView('listings/edit',[
                'listing' => $listing,
                'errors' => $errors
            ]);
            exit;
        } else {
            //inspect('Success');
            //Submit to database
            $updateFields = [];

            foreach(array_keys($updateValues) as $field){
                $updateFields[] = "{$field} = :{$field}";
            }
            //inspect($updateFields);

            $updateFields = implode(',', $updateFields);
            //inspect($updateFields);
            
            $updateQuery = "UPDATE workopia.listings SET {$updateFields} WHERE id = :id";

            inspect($updateQuery);
            $updateValues["id"] = $id;

            $this->db->query($updateQuery, $updateValues);

            //$_SESSION['success_message'] = 'List updated';
            Session::setFlashMessage('success_message','List updated successfully');

            redirect("/listings/{$id}");

        }

    }


    /**
     * Find and return listings which match the the provided terms
     * @return void
     */
    public function search($params){
        $keywords = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';
        $location = isset($_GET['location']) ? trim($_GET['location']) : '';

        //inspect($keywords);

        $query = "SELECT * FROM workopia.listings WHERE 
        (title LIKE :keywords OR tags LIKE :keywords OR company LIKE :keywords)
        AND 
        (city LIKE :location OR state LIKE :location)"; 
        $params = [
            'keywords' => "%{$keywords}%",
            'location' => "%{$location}%"
        ];

        $listings = $this->db->query($query,$params)->fetchAll();
        
        //inspectAndDie($listings);

        loadView('/listings/index' ,[
            'listings' => $listings,
            'keywords' => $keywords,
            'location' => $location
        ]);
    }

}

?>