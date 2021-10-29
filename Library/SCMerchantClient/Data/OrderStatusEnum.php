<?php

namespace Cryptum\Cryptum\Library\SCMerchantClient\Data;

class OrderStatusEnum {
	public static $New = 1;
	public static $Pending = 'pending';
	public static $Paid = 'confirmed';
	public static $Failed = 'failed';
	public static $Expired = 'cancelled';
	public static $Test = 6;

} 
