<?php namespace App\Controllers;

class Equation extends BaseController
{
    /**
     * Function that solves quadratic equation
     *
     */
    public function solve()
    {
        // Validation
        $validation = \Config\Services::validation();

        $validation->setRules([
            'a' => 'required|integer|not_zero',
            'b' => 'required|integer',
            'c' => 'required|integer',
            'token' => 'required|abctoken[1]',
        ])
            ->withRequest($this->request);

        if (!$validation->run()) {
            $errors = $validation->getErrors();
            return $this->response->setStatusCode(404)->setJSON([
                'status' => -1,
                'message' => reset($errors),
            ]);
        }

        // Request variables
        $a = $this->request->getVar('a');
        $b = $this->request->getVar('b');
        $c = $this->request->getVar('c');
        $token = $this->request->getVar('token');

        // Check if request with given a, b and c already exist and get saved response
        $db = \Config\Database::connect();
        $builder = $db->table('requests');
        $existing_request = $builder->select('requests.id, response')
            ->join('responses', 'requests.id = responses.request_id')
            ->where('token', $token)
            ->get()
            ->getRow();

        if ($existing_request) {
            // Sent request exist in database, increase it's count
            $builder = $db->table('requests');
            $builder->set('count', 'count + 1', FALSE)
                ->where('id', $existing_request->id)
                ->update();

            return $this->response->setStatusCode(200)->setJSON(
                json_decode($existing_request->response)
            );
        }

        $request_vars = $this->request->getPost();
        $request_vars['count'] = 1;

        // Saving request to database
        $requestModel = new \App\Models\RequestModel();
        $requestModel->insert($request_vars);
        $request_id = $requestModel->getInsertID();

        // Calculating Discriminant
        $d = $b * $b - 4 * $a * $c;

        // Build response data based on Discriminant value
        if ($d < 0) {
            // Equation have no solution
            $response_data = [
                'status' => 3,
                'message' => lang('equation.no_solution'),
            ];
        } elseif ($d == 0) {
            // Equation have 1 solution
            $response_data = [
                'status' => 2,
                'message' => lang('equation.1_solution'),
                'x1' => (-$b / 2 * $a),
            ];
        } else {
            // Equation have 2 solutions
            $response_data = [
                'status' => 1,
                'message' => lang('equation.2_solutions'),
                'x1' => ((-$b + sqrt($d)) / (2 * $a)),
                'x2' => ((-$b - sqrt($d)) / (2 * $a)),
            ];
        }

        // Saving generated response to database
        $responseModel = new \App\Models\ResponseModel();
        $responseModel->insert([
            'request_id' => $request_id,
            'response' => json_encode($response_data),
        ]);

        return $this->response->setStatusCode(200)->setJSON($response_data);
    }

}
