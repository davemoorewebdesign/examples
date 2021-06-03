<?php
namespace App;

use App\Modules\DispatchSystem\Models\Storage;
use App\Modules\DispatchSystem\Models\BatchManager;
use App\Modules\DispatchSystem\Models\CourierFactory;


// Get non-specific storage instance (accessed globally in Record class for demonstration purposes)... this might normally be a factory to allow multiple connections
$storage = Storage::getInstance();


// Demonstration of DispatchSystem primary functions

// Get BatchManager instance
$batch_manager = BatchManager::getInstance();

// 1. Start a batch (doesn't return a Batch so consignments can't be added after
$batch = $batch_manager->startBatch();

// 2. Add a consignment and return a consignment object to access properties such as UIN (unique identification number)
$courier = CourierFactory::create('ANC');
$consignment = $batch->addConsignment($courier);
$consignment_uin = $consignment->geUin();

// 3. End a batch
$batch_manager->endBatch();


// Bonus: Check if there is an active batch for today so interface dev can safely run the other methods (stops an exception being thrown)
$has_active_batch = $batch_manager->hasActiveBatch();

// Bonus: When a batch has already been started, it can also be returned via...
$batch = $batch_manager->getActiveBatch();

// Example of bonus method
if (!$has_active_batch) {
    $batch = $batch_manager->startBatch();
}