<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class DatabaseHelper
{
    /**
     * Get the SQL function for extracting year from a date
     * 
     * @param string $column
     * @return string
     */
    public static function yearFunction(string $column): string
    {
        if (config('database.default') === 'sqlite') {
            return "strftime('%Y', $column)";
        }
        
        return "YEAR($column)";
    }
    
    /**
     * Get the SQL function for extracting month from a date
     * 
     * @param string $column
     * @return string
     */
    public static function monthFunction(string $column): string
    {
        if (config('database.default') === 'sqlite') {
            return "strftime('%m', $column)";
        }
        
        return "MONTH($column)";
    }
    
    /**
     * Get the SQL function for extracting day from a date
     * 
     * @param string $column
     * @return string
     */
    public static function dayFunction(string $column): string
    {
        if (config('database.default') === 'sqlite') {
            return "strftime('%d', $column)";
        }
        
        return "DAY($column)";
    }
    
    /**
     * Get the SQL function for date formatting
     * 
     * @param string $column
     * @param string $format
     * @return string
     */
    public static function dateFormat(string $column, string $format): string
    {
        if (config('database.default') === 'sqlite') {
            // Convert MySQL format to SQLite strftime format
            $sqliteFormat = str_replace([
                '%Y', '%m', '%d', '%H', '%i', '%s',
                '%M', '%b', '%D', '%y', '%h', '%p'
            ], [
                '%Y', '%m', '%d', '%H', '%M', '%S',
                '%m', '%m', '%d', '%y', '%H', '%p'
            ], $format);
            
            return "strftime('$sqliteFormat', $column)";
        }
        
        return "DATE_FORMAT($column, '$format')";
    }
    
    /**
     * Get a date query that works across different databases
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $column
     * @param string $operator
     * @param mixed $value
     * @return \Illuminate\Database\Query\Builder
     */
    public static function whereDate($query, string $column, string $operator, $value)
    {
        if (config('database.default') === 'sqlite') {
            return $query->whereRaw("date($column) $operator ?", [$value]);
        }
        
        return $query->whereDate($column, $operator, $value);
    }
    
    /**
     * Get a year query that works across different databases
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $column
     * @param mixed $value
     * @return \Illuminate\Database\Query\Builder
     */
    public static function whereYear($query, string $column, $value)
    {
        if (config('database.default') === 'sqlite') {
            return $query->whereRaw("strftime('%Y', $column) = ?", [$value]);
        }
        
        return $query->whereYear($column, $value);
    }
    
    /**
     * Get a month query that works across different databases
     * 
     * @param \Illuminate\Database\Query\Builder $query
     * @param string $column
     * @param mixed $value
     * @return \Illuminate\Database\Query\Builder
     */
    public static function whereMonth($query, string $column, $value)
    {
        if (config('database.default') === 'sqlite') {
            return $query->whereRaw("strftime('%m', $column) = ?", [sprintf('%02d', $value)]);
        }
        
        return $query->whereMonth($column, $value);
    }
}