<?php

class Event extends Entry
{
	const STATUS_DRAFT=1;
	const STATUS_PUBLISHED=2;
	const STATUS_ARCHIVED=3;
	
	public $Town;
	public $RegionID;
	public $VerifyCode;
	public $PageTypeID = 4;
	
	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		$relations = parent::relations();
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		$newRelations = array(
			'Genre' => array(self::BELONGS_TO, 'Genre', 'GenreID'),
			/*'Region' => array(self::BELONGS_TO, 'Region', 'RegionID'),
			'Country' => array(self::BELONGS_TO, 'Country', 'CountryID'),*/
			'Organiser' => array(self::BELONGS_TO, 'Organiser', 'OrganiserID'),
			'Venue' => array(self::BELONGS_TO, 'Venue', 'VenueID'),
			'Reviews'=>array(self::HAS_MANY, 'Review', 'EventID'),
		);
		return $relations + $newRelations;
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		$rules = array(
			//array('Title, SiteName, Address1, Town, RegionID, DateFrom, CountryID, ContactEmail', 'required'),
			array('Title, VenueID, DateFrom, ContactEmail', 'required'),
			array('AuthorEmail', 'email'),
			array('Status', 'in', 'range'=>array(0,1)),
			//array('AccessCode, Lng, Lat, RegionID, OrganiserID, CountryName, Title, SiteName, Address2, Postcode, Prices, ContactTel', 'length', 'max'=>128),
			array('AccessCode, OrganiserID, RegionID, Town, Title, Prices, ContactTel', 'length', 'max'=>128),
			array('GenreID', 'in', 'range'=>Genre::getIDs()),
			//array('OrganiserID, UserID','numerical','allowEmpty'=>true),
			array('Description, Price', 'length', 'allowEmpty'=>true),
			array('DateFrom, DateTo', 'length', 'max'=>20),
			array('FacebookEventUrl, DiscussionUrl, OrganisersUrl, BookingFormUrl, BookingOnlineUrl','url','allowEmpty'=>true),
			array('ContactEmail','email'),
			array('VerifyCode', 'captcha', 'allowEmpty'=>(!CCaptcha::checkRequirements() || !Yii::app()->user->isGuest)),
			array('Title, EventID, Title, GenreID, RegionID, Town, OrganiserID, VenueID, DateFrom, DateTo, UserID, Status, Updated, Created', 'safe', 'on'=>'search'),
		);
		
		if (Yii::app()->user->isGuest) {
			$rules[] = array('AuthorEmail', 'required');
		}
		
		return $rules;
	}
	
	public function attributeLabels()
	{
		return array(
			'Title' => 'Event Name',
			'GenreID'=>'Genre',
			'EventID'=>'ID',
			//'RegionID'=>'Region',
			'VenueID'=>'Venue',
			'VerifyCode'=>'Verification Code',
			'FacebookEventUrl'=>'Facebook Event Link',
			'DiscussionUrl'=>'Discussion Link', 
			'OrganisersUrl'=>'Organisers Link', 
			//'SiteUrl'=>'Site Link',
			'BookingFormUrl'=>'Booking Form Link', 
			'BookingOnlineUrl'=>'Booking Online Link',
			'OrganiserID'=>'Organiser / Campaign',
		);
	}
	
	public function getUrl()
	{
		return Yii::app()->createUrl('event/view', array(
			'id'=>$this->EventID,
			'title'=>Tools::urlSafe($this->Title),
		));
	}
	
	public function getFormattedDescription() {
		if ($this->EventID > 214) {
			return nl2br($this->Description);
		} else {
			return $this->Description;
		}
	}
	
	public function getOrganiserName() {
		return $this->Organiser->OrganiserName;
	}
	
	protected function beforeValidation()
	{
		if(parent::beforeValidation())
		{	
			$parts = explode("/", $this->DateFrom);
			$this->DateFrom = $parts[2]."-".$parts[1]."-".$parts[0]." 00:00:00";
			
			$parts = explode("/", $this->DateTo);
			$this->DateTo = $parts[2]."-".$parts[1]."-".$parts[0]." 00:00:00";
			
			$this->Status = Yii::app()->user->isGuest?0:1;
			
			if (!Yii::app()->user->isGuest && ($this->getIsNewRecord() || !$this->AuthorEmail)) {
				$this->AuthorEmail = $this->userData->Email;
			}
			
			if ($this->EventID > 214) {
				$this->Description = strip_tags($this->Description);
			}
			return true;
		}
		else
			return false;
	}
	
	protected function beforeSave()
	{
		if (!$this->AccessCode) {
			if (!$this->EventID) {
				$lastEvent = Event::model()->find(array('order'=>'EventID DESC', 'limit'=>1));
				$id = $lastEvent->EventID + 1;
			} else {
				$id = $this->EventID;
			}
			$randomCode = rand(100000, 999999);
			$this->AccessCode = "E".$id.$randomCode;
		}
		$this->AuthorEmail = strtolower($this->AuthorEmail);
		$this->ContactEmail = strtolower($this->ContactEmail);
		
		return parent::beforeSave();
	}
	
	public function getCriteria()
	{
		$criteria = new CDbCriteria;
		$criteria->with = array('Venue');
		
		if (!$this->DateFrom)
		{
			$this->DateFrom = date('d/m/Y');
		}
		if ($this->EventID)
		{
			$criteria->addCondition("t.EventID =".$this->EventID);
		}
		if ($this->UserID > 0)
		{
			$criteria->addCondition("t.UserID =".$this->UserID);
		}
		if ($this->Title)
		{
			$criteria->addCondition("t.Title LIKE '%".$this->Title."%'");
		}
		if ($this->OrganiserID > 1)
		{
			$criteria->addCondition("t.OrganiserID =".$this->OrganiserID);
		}
		if ($this->Town)
		{
			$criteria->addCondition("Venue.Town LIKE '%".$this->Town."%'");
		}
		if ($this->RegionID)
		{
			$criteria->addCondition("Venue.RegionID = ".$this->RegionID);
		}
		if ($this->GenreID)
		{
			$criteria->join .= " LEFT JOIN Genre g ON t.GenreID=g.GenreID";
			$criteria->addCondition("g.GenreID = ".$this->GenreID);
		}
		
		if ($this->Status !== null) {
			$criteria->addCondition("t.Status = ".$this->Status);
		}
		
		$parts = explode("/", $this->DateFrom);
		$DateFrom = $parts[2]."-".$parts[1]."-".$parts[0]." 00:00:00";
		$criteria->addCondition("t.DateFrom >= '".date("Y-m-d", strtotime($DateFrom))."'");
		
		return $criteria;
	}
 
	/**
	 * Retrieves the list of events based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the required events.
	 */
	public function search()
	{	
		$criteria = $this->getCriteria();
		
		return new CActiveDataProvider('Event', array(
			'criteria'=>$criteria,
			 'pagination'=>array('pageSize'=>25),
			'sort'=>array(
				//'defaultOrder'=>'t.DateFrom ASC, t.DateTo ASC, t.Town ASC',
				'defaultOrder'=>'t.DateFrom ASC, t.DateTo ASC',
			),
		));
	}
	
	public function getSiteName() {
		return $this->Venue->Name;
	}
	
	public function getSiteNameFormatted() {
		if (strlen($this->Venue->Name) > 22) {
			$name = substr($this->Venue->Name, 0, 19).'...';
		} else {
			$name = $this->Venue->Name;
		}
		return CHtml::link($name, $this->Venue->url);
	}
	public function getOrganiserNameFormatted() {
		return CHtml::link($this->Organiser->Name, $this->Organiser->url);
	}
	
	public function getDateFromFormatted() {
		return date('d/m/Y', strtotime($this->DateFrom));
	}
	
	public function getDateToFormatted() {
		return date('d/m/Y', strtotime($this->DateTo));
	}
	
	public function getGenreName() {
		return $this->Genre->Name;
	}
}
?>
