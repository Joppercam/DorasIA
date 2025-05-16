<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;
use App\Helpers\DatabaseHelper;
use Illuminate\Support\Facades\Config;

class DatabaseHelperTest extends TestCase
{
    public function test_year_function_returns_sqlite_format_for_sqlite()
    {
        Config::set('database.default', 'sqlite');
        
        $result = DatabaseHelper::yearFunction('created_at');
        
        $this->assertEquals("strftime('%Y', created_at)", $result);
    }
    
    public function test_year_function_returns_mysql_format_for_mysql()
    {
        Config::set('database.default', 'mysql');
        
        $result = DatabaseHelper::yearFunction('created_at');
        
        $this->assertEquals("YEAR(created_at)", $result);
    }
    
    public function test_month_function_returns_sqlite_format_for_sqlite()
    {
        Config::set('database.default', 'sqlite');
        
        $result = DatabaseHelper::monthFunction('created_at');
        
        $this->assertEquals("strftime('%m', created_at)", $result);
    }
    
    public function test_month_function_returns_mysql_format_for_mysql()
    {
        Config::set('database.default', 'mysql');
        
        $result = DatabaseHelper::monthFunction('created_at');
        
        $this->assertEquals("MONTH(created_at)", $result);
    }
    
    public function test_date_format_conversion()
    {
        Config::set('database.default', 'sqlite');
        
        $result = DatabaseHelper::dateFormat('created_at', '%Y-%m-%d');
        
        $this->assertEquals("strftime('%Y-%m-%d', created_at)", $result);
        
        Config::set('database.default', 'mysql');
        
        $result = DatabaseHelper::dateFormat('created_at', '%Y-%m-%d');
        
        $this->assertEquals("DATE_FORMAT(created_at, '%Y-%m-%d')", $result);
    }
}