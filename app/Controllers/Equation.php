<?php namespace App\Controllers;

class Equation extends BaseController
{

    public function solve()
    {
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

        $a = $this->request->getVar('a');
        $b = $this->request->getVar('b');
        $c = $this->request->getVar('c');
        $token = $this->request->getVar('token');

        $db = \Config\Database::connect();
        $builder = $db->table('requests');
        $existing_request = $builder->select('requests.id, response')
            ->join('responses', 'requests.id = responses.request_id')
            ->where('token', $token)
            ->get()
            ->getRow();

        if ($existing_request) {
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

        $requestModel = new \App\Models\RequestModel();
        $requestModel->insert($request_vars);
        $request_id = $requestModel->getInsertID();

        $d = $b * $b - 4 * $a * $c;

        if ($d < 0) {
            $response_data = [
                'status' => 3,
                'message' => 'No solution found',
            ];
        } elseif ($d == 0) {
            $response_data = [
                'status' => 2,
                'message' => '1 solution found',
                'x1' => (-$b / 2 * $a),
            ];
        } else {
            $response_data = [
                'status' => 1,
                'message' => '2 solutions found',
                'x1' => ((-$b + sqrt($d)) / (2 * $a)),
                'x2' => ((-$b - sqrt($d)) / (2 * $a)),
            ];
        }

        $responseModel = new \App\Models\ResponseModel();
        $responseModel->insert([
            'request_id' => $request_id,
            'response' => json_encode($response_data),
        ]);

        return $this->response->setStatusCode(200)->setJSON($response_data);
    }

}
