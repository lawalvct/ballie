<?php

// Add this temporary debugging method to your OnboardingController if needed
// You can call it with: $this->debugDatabaseOperation('description', function() { /* db operation */ });

private function debugDatabaseOperation($description, $callback)
{
    Log::info("Starting database operation: {$description}");

    try {
        // Always refresh connection before any operation
        $this->refreshDatabaseConnection();

        // Execute the operation
        $result = $callback();

        Log::info("Successfully completed database operation: {$description}");
        return $result;

    } catch (\Exception $e) {
        Log::error("Database operation failed: {$description}", [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ]);

        // Try once more after connection refresh
        try {
            Log::info("Retrying database operation after connection refresh: {$description}");
            $this->refreshDatabaseConnection();
            $result = $callback();
            Log::info("Retry successful for database operation: {$description}");
            return $result;
        } catch (\Exception $retryException) {
            Log::error("Retry also failed for database operation: {$description}", [
                'error' => $retryException->getMessage(),
                'line' => $retryException->getLine(),
            ]);
            throw $retryException;
        }
    }
}

// Example usage in your methods:
// $this->debugDatabaseOperation('Update tenant data', function() use ($tenant, $data) {
//     $tenant->update($data);
// });
//
// $this->debugDatabaseOperation('Update progress', function() use ($tenant, $progress) {
//     $tenant->update(['onboarding_progress' => $progress]);
// });
