<?php

/*

Health analysis PHP Class

Developed by Vinicius Souza <vmsouza@vmsouza.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>5.
*/

/**
  * Class used for health analysis results.
  *
  * This class returns in object format:
  *
  * BMI
  * Harris Benedict BMR TDEE
  * Katch McArdle BMR and TDEE (with Jackson & Pollock 7 skinfold protocol results)
  * Jackson and Pollock 7 Skinsfold Protocol (sum of skinfolds, body density, body fat,
  * fat mass and lean mass)
  *
  * It is not necessary to fill in all the fields because the class will only return data
  * pertaining to the measurements provided.
  *
  * Sample code:
  * <pre>
  * &lt;?php
  *
  * require_once("healt.class.php");
  * $h=new Health();
  *
  * $h->age=46;
  * $h->gender="male";
  * $h->height=168;
  * $h->weight=80;
  *
  * $h->activity=1.2;
  *
  * $h->triceps=6;
  * $h->chest=9;
  * $h->subscapular=13;
  * $h->midaxillary=9;
  * $h->suprailiac=8;
  * $h->abdominal=15;
  * $h->thigh=11;
  *
  * $data=$h->getdata();
  * print_r($data);
  *
  * ?&gt;
  * </pre>
  *
  * @author Vinicius Souza <vmsouza@vmsouza.com>
  * @version 0.1
  * @copyright GPL-3.0 license
  * @access public
  * @package api
  * @example new \api\Page("template.tpl")
  */
class Health {

	/* public measurements */
	public $age, $gender, $height, $weight;
	public $bmi, $bmicat;
	public $activity;
	public $triceps, $chest, $subscapular, $midaxillary, $suprailiac, $abdominal, $thigh;

	/* measurements for jackson & pollock 7 skinsfold protocol */
	protected $skinfolds7=array("triceps","chest","subscapular","midaxillary","suprailiac","abdominal","thigh");

	/* result data */
	protected $data;


	/**
      * Constructor
      * Class constructor
      *
      * @access public
      * @return void
      */
	public function __construct() {
		$this->data=new Stdclass();
	}

	/**
      * BMI Calculator
      *
      * @access private
      * @return void
      */
	private function bmi() {

		if (!is_numeric($this->weight) and !is_int($this->weight))
			return;

		if (!is_numeric($this->height) and !is_int($this->height))
			return;

		if (!is_numeric($this->age) and !is_int($this->age))
			return;

		$bmi=($this->weight/($this->height/100)**2);

		if ($bmi <= 16)
			$bmicat="Several Thinness";
		else if ($bmi >16 and $bmi <= 17)
			$bmicat="Moderate Thinness";
		else if ($bmi > 17 and $bmi <= 18.5)
			$bmicat="Mild Thinness";
		else if ($bmi > 18.5 and $bmi <= 25)
			$bmicat="Normal";
		else if ($bmi > 25 and $bmi <= 30)
			$bmicat="Overheight";
		else if ($bmi > 30 and $bmi <= 35)
			$bmicat="Obese Class I";
		else if ($bmi < 35 and $bmi <= 40)
			$bmicat="Obese Class II";
		else
			$bmicat="Obese Class III";

		$this->bmi=$bmi;
		$this->bmicat=$bmicat;

		$this->data->measurements->bmi=new Stdclass();
		$this->data->measurements->bmi->value=round($this->bmi,1);
		$this->data->measurements->bmi->category=$this->bmicat;

	}

	/**
      * Harris Benedict BMR Calculator
      *
      * @access private
      * @return void
      */
	private function bmr_harris_benedict() {
		if ($this->gender !="male" and $this->gender!="female")
			return;

		if (!is_numeric($this->weight) and !is_int($this->weight))
			return;

		if (!is_numeric($this->height) and !is_int($this->height))
			return;

		if (!is_numeric($this->age) and !is_int($this->age))
			return;

		switch($this->gender) {
			case "male":
				$bmr=88+(13.4*$this->weight)+(4.8*$this->height)-(5.7*$this->age);
				break;
			case "female":
				$bmr=448+(9.2*$this->weight)+(3.1*$this->height)-(4.3*$this->age);
				break;
			default:
				return;
		}

		$this->tmb_harris_benedict=$bmr;
		if (!isset($this->data->measurements->harris_benedict))
			$this->data->measurements->harris_benedict=new Stdclass();

		$this->data->measurements->harris_benedict->bmr=ceil($bmr);

		if (!is_numeric($this->activity))
			return;

		if ($this->activity < 1 or $this->activity > 2)
			return;

		if (!isset($this->data->tdee))
			$this->data->measurements->harris_benedict->tdee=ceil($bmr*$this->activity);

		return;

	}

	/**
      * Jackson & Pollock 7 skinfolds protocol calculator
      *
      * @access private
      * @return void
      */
	private function jackson_pollock_7_skinsfold_protocol() {

		$name="prot_7_skinfolds";


		foreach($this->skinfolds7 as $idx => $skinfold) {
			if (!isset($this->$skinfold) or !is_int($this->$skinfold))
				return;
		}

		if (!isset($this->data->$name))
			$this->data->measurements->$name=new stdClass();

		$skinfoldsum=0;
		foreach($this->skinfolds7 as $idx => $skinfold) {
			$skinfoldsum+=$this->$skinfold;
		}

		$this->data->measurements->$name->skinfoldsum=$skinfoldsum;

		switch($this->gender) {
			case "male":
				$body_density=1.112-(0.00043499*$skinfoldsum)+(0.00000055*($skinfoldsum**2))-(0.00028826*$this->age);
				break;
			case "female":
				$body_density=1.097-(0.00046971*$skinfoldsum)+(0.00000056*($skinfoldsum^2))-(0.00012828*$this->age);
				break;
			default:
				return;
		}
		$this->data->measurements->$name->body_density=$body_density;

		$bf=(4.95/$body_density)-4.5;
		$this->data->measurements->$name->bf=round($bf*100,2);

		$fatmass=$bf*$this->weight;
		$this->data->measurements->$name->fatmass=round($fatmass,3);

		$leanmass=$this->weight-$this->data->measurements->$name->fatmass;
		$this->data->measurements->$name->leanmass=$leanmass;

	}

	/**
      * Katch McArdle BMR Calculator based on Jackson & Pollock 7 skinfolds protocol calculator result
      *
      * @access private
      * @return void
      */
	private function bmr_katch_mcardle() {
		if (!isset($this->data->measurements->prot_7_skinfolds->leanmass) or !is_numeric($this->data->measurements->prot_7_skinfolds->leanmass))
			return;

		$bmr=370+(21.6*$this->data->measurements->prot_7_skinfolds->leanmass);
		$this->data->measurements->katch_mcardle=new Stdclass();
		$this->data->measurements->katch_mcardle->bmr=ceil($bmr);

		if (isset($this->activity) and is_numeric($this->activity))
			$this->data->measurements->katch_mcardle->tdee=ceil($bmr*$this->activity);
	}

	/**
      * Mifflin-St Jeor BMR Calculator
      *
      * @access private
      * @return void
      */
	private function bmr_mifflin_st_jeor() {

		if ($this->gender !="male" and $this->gender!="female")
			return;

		if (!is_numeric($this->weight) and !is_int($this->weight))
			return;

		if (!is_numeric($this->height) and !is_int($this->height))
			return;

		if (!is_numeric($this->age) and !is_int($this->age))
			return;

		switch($this->gender) {
			case "male":
				$bmr=(10*$this->weight)+(6.25*$this->height)-(5*$this->age)+5;
				break;
			case "female":
				$bmr=(10*$this->weight)+(6.25*$this->height)-(5*$this->age)-161;
				break;
			default:
				return;
		}

		if (!isset($this->data->measurements->mifflin_st_jeor))
			$this->data->measurements->mifflin_st_jeor=new Stdclass();

		$this->data->measurements->mifflin_st_jeor->bmr=ceil($bmr);

		if (!is_numeric($this->activity))
			return;

		if ($this->activity < 1 or $this->activity > 2)
			return;

		$this->data->measurements->mifflin_st_jeor->tdee=ceil($bmr*$this->activity);
	}

	/**
      * Get Data generated by all calculators
      *
      * @access private
      * @return object data Object class with all results
      */
	public function getdata() {

		$this->data->measurements=new Stdclass();

		if (is_numeric($this->height))
			$this->data->measurements->height=$this->height;

		if (is_numeric($this->weight))
			$this->data->measurements->weight=$this->weight;

		if (is_numeric($this->age))
			$this->data->measurements->age=$this->age;

		if ($this->gender=="male" or $this->gender=="female")
			$this->data->measurements->gender=$this->gender;

		$this->bmi();

		$this->data->measurements->skinfolds=new Stdclass();
		foreach($this->skinfolds7 as $idx => $skinfold) {
			if (is_numeric($this->$skinfold))
				$this->data->measurements->skinfolds->$skinfold=$this->$skinfold;
		}

		$this->jackson_pollock_7_skinsfold_protocol();

		$this->bmr_harris_benedict();

		$this->bmr_katch_mcardle();

		$this->bmr_mifflin_st_jeor();

		return $this->data;
	}

}

?>
