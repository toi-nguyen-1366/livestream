<?php
namespace Decidir\PartialRefund;

include_once dirname(__FILE__)."/../Data/AbstractData.php";

class Data extends \Decidir\Data\AbstractData {

	public function __construct(array $data) {
		$this->setOptionalFields(array(
			"sub_payments" => array(
				"name" => array(
					"id" => array(
                        "name"=> "id"
                    ),
					"amount" => array(
                        "name"=> "amount"
                    ),
				)
				),
				"amount" => array(
					"name"=> "amount"
				)
		));

		parent::__construct($data);
	}

	public function getData(){
		return json_encode($this->getDataField());
	}
}