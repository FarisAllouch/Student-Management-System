<?php

require_once __DIR__ . '/../services/ProfessorService.class.php';

Flight::set('professor_service', new ProfessorService());

Flight::group('/professors', function(){

    Flight::route('POST /add', function() {
        $payload = Flight::request()->data->getData();
    
        if ($payload['FullName'] == NULL || $payload['FullName'] == '') {
            Flight::halt(500, "Full name field is missing");
        }
    
        if ($payload['id'] != NULL && $payload['id'] != '') {
            $professor = Flight::get('professor_service')->edit_professor($payload);
        } else {
            unset($payload['id']);
            $professor = Flight::get('professor_service')->add_professor($payload);
        }
    
        Flight::json(['message' => "You have successfully added the professor", 'data' => $professor]);
    });

    Flight::route('GET /', function() {
    
        $payload = Flight::request()->query;
    
        $params = [
            'start' => (int)$payload['start'],
            'search' => $payload['search']['value'],
            'draw' => $payload['draw'],
            'limit' => (int)$payload['length'],
            'order_column' => $payload['order'][0]['name'],
            'order_direction' => $payload['order'][0]['dir']
        ];
    
        $data = Flight::get('professor_service')->get_professors_paginated($params['start'], $params['limit'], $params['search'], $params['order_column'], $params['order_direction']);
    
        foreach ($data['data'] as $id => $professor) {
            $data['data'][$id]['action'] = '<div class="btn-group" role="group" aria-label="Actions">' .
                                                '<button type="button" class="btn btn-warning" onclick="ProfessorService.open_edit_professor_modal('. $professor['id'] .')">Edit</button>' .
                                                '<button type="button" class="btn btn-danger" onclick="ProfessorService.delete_professor('. $professor['id'] .')">Delete</button>' .
                                            '</div>';
        }
    
        //Response
        Flight::json([
            'draw' => $params['draw'],
            'data' => $data['data'],
            'recordsFiltered' => $data['count'],
            'recordsTotal' => $data['count'],
            'end' => $data['count']
        ], 200);
    });
    
    Flight::route('DELETE /delete/@professor_id', function($professor_id) {
        
        if ($professor_id == NULL || $professor_id == '') {
            Flight::halt(500, "You have to provide a professor id!");
        }
    
        Flight::get('professor_service')->delete_professor_by_id($professor_id);
        Flight::json(['message'=> "You have successfully deleted the professor!"]);
    });
    
    Flight::route('GET /@professor_id', function($professor_id) {
        $professor = Flight::get('professor_service')->get_professor_by_id($professor_id);
    
        Flight::json($professor, 200);
    });
});