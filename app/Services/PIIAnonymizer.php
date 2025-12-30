<?php

namespace App\Services;

class PIIAnonymizer
{
    /**
     * Anonymize personally identifiable information from text
     */
    public static function anonymize(string $text): string
    {
        // Remove email addresses
        $text = preg_replace('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', '[EMAIL]', $text);
        
        // Remove phone numbers (various formats)
        $text = preg_replace('/(\+\d{1,3}[-.\s]?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}/', '[PHONE]', $text);
        $text = preg_replace('/\d{3}[-.\s]?\d{3}[-.\s]?\d{4}/', '[PHONE]', $text);
        
        // Remove addresses (basic pattern - street numbers and common street types)
        $text = preg_replace('/\d+\s+[\w\s]+\s+(Street|St|Avenue|Ave|Road|Rd|Boulevard|Blvd|Lane|Ln|Drive|Dr|Court|Ct|Circle|Cir|Way|Place|Pl)/i', '[ADDRESS]', $text);
        
        // Remove social security numbers
        $text = preg_replace('/\d{3}-\d{2}-\d{4}/', '[SSN]', $text);
        
        // Remove dates of birth (various formats)
        $text = preg_replace('/\b(0?[1-9]|1[0-2])[\/\-](0?[1-9]|[12][0-9]|3[01])[\/\-](19|20)?\d{2}\b/', '[DOB]', $text);
        
        // Remove URLs (but keep domain for context if needed)
        $text = preg_replace('/https?:\/\/[^\s]+/', '[URL]', $text);
        
        return $text;
    }
    
    /**
     * Anonymize PII from array data
     */
    public static function anonymizeArray(array $data): array
    {
        $sensitiveFields = ['email', 'phone', 'address', 'ssn', 'dateOfBirth', 'linkedin', 'website'];
        
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = self::anonymizeArray($value);
            } elseif (is_string($value)) {
                // Check if field name suggests PII
                if (in_array(strtolower($key), $sensitiveFields)) {
                    $data[$key] = '[REDACTED]';
                } else {
                    // Still scan the content for PII patterns
                    $data[$key] = self::anonymize($value);
                }
            }
        }
        
        return $data;
    }
    
    /**
     * Check if text contains PII that should be anonymized
     */
    public static function containsPII(string $text): bool
    {
        // Check for email
        if (preg_match('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $text)) {
            return true;
        }
        
        // Check for phone
        if (preg_match('/(\+\d{1,3}[-.\s]?)?\(?\d{3}\)?[-.\s]?\d{3}[-.\s]?\d{4}/', $text)) {
            return true;
        }
        
        // Check for SSN
        if (preg_match('/\d{3}-\d{2}-\d{4}/', $text)) {
            return true;
        }
        
        return false;
    }
}
