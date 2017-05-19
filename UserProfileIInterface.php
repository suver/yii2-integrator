<?php
namespace common\modules\users;

interface UserProfileIInterface
{

	public function getRoute();

	public function view();

	public function tab();

	public function form();

}