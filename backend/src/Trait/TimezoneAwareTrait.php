<?php

namespace App\Trait;

use DateTimeImmutable;
use DateTimeZone;

trait TimezoneAwareTrait
{
    /**
     * Format a DateTimeImmutable in a specific timezone
     * 
     * @param DateTimeImmutable|null $date The date to format (stored in UTC)
     * @param string $timezone The timezone to convert to (e.g., 'Europe/London')
     * @param string $format The output format (default: 'Y-m-d H:i:s')
     * @return string|null The formatted date string or null
     */
    private function formatInTimezone(?DateTimeImmutable $date, string $timezone, string $format = 'Y-m-d H:i:s'): ?string
    {
        if (!$date) {
            return null;
        }

        try {
            $timezoneObj = new DateTimeZone($timezone);
            $convertedDate = $date->setTimezone($timezoneObj);
            return $convertedDate->format($format);
        } catch (\Exception $e) {
            // Fallback to UTC if timezone is invalid
            return $date->format($format);
        }
    }

    /**
     * Format a DateTimeImmutable with timezone suffix
     * 
     * @param DateTimeImmutable|null $date The date to format
     * @param string $timezone The timezone to convert to
     * @return string|null The formatted date with timezone (e.g., '2026-02-15 14:30:00 GMT')
     */
    private function formatWithTimezone(?DateTimeImmutable $date, string $timezone): ?string
    {
        if (!$date) {
            return null;
        }

        try {
            $timezoneObj = new DateTimeZone($timezone);
            $convertedDate = $date->setTimezone($timezoneObj);
            return $convertedDate->format('Y-m-d H:i:s T');
        } catch (\Exception $e) {
            return $date->format('Y-m-d H:i:s \U\T\C');
        }
    }

    /**
     * Parse a date string from a specific timezone to UTC DateTimeImmutable
     * Useful when accepting dates from the frontend in user's timezone
     * 
     * @param string $dateString The date string (e.g., '2026-02-15 14:30:00')
     * @param string $fromTimezone The timezone of the input date
     * @return DateTimeImmutable The date in UTC
     */
    private function parseFromTimezone(string $dateString, string $fromTimezone): DateTimeImmutable
    {
        try {
            $timezoneObj = new DateTimeZone($fromTimezone);
            $date = new DateTimeImmutable($dateString, $timezoneObj);
            // Convert to UTC for storage
            return $date->setTimezone(new DateTimeZone('UTC'));
        } catch (\Exception $e) {
            // If parsing fails, assume UTC
            return new DateTimeImmutable($dateString, new DateTimeZone('UTC'));
        }
    }
}
