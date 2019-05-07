<?php
// namespace App\Libraries;

class JSend
{
    static public function success($message, $data = array(), $response_code = 200) {
		return JSend::situation('success', $message, $data, $response_code);
	}

	static public function fail($message, $data = array(), $response_code = 404) {
		return JSend::situation('fail', $message, $data, $response_code);
	}

	static public function error($message, $data = array(), $response_code = 500) {
		return JSend::situation('error', $message, $data, $response_code);
	}

	static public function situation($status, $message, $data, $response_code = 200) {
		$template = array(
			'success'	=> true,
			'error'		=> false,
			'status'	=> 'success',
			'data'		=> null
		);

		if($status == 'error') {
			$template['error'] = true;
			$template['success'] = false;
			$template['fail'] = false;

			$template['message'] = $message;

		} else if($status == 'fail') {
			$template['error'] = true;
			$template['success'] = false;
			$template['fail'] = true;

			$template['data'] = array($message);
		}

		$template['status'] = $status;

		if(is_string($message)) {
			$template[$status] = $message;

		} elseif(is_array($message)) {
			$template = array_merge($template, $message);
		} 

		if($data)
			$template['data'] = $data;

		return response(json_encode($template), $response_code)->header('Content-type', 'application/json');
	}
}
