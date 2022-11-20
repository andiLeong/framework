<?php

namespace Andileong\Framework\Core\tests;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class GrammarSelectSqlTest extends testcase
{
    /** @test */
    public function it_can_convert_multiple_columns()
    {
        $expected = "select `id`, `username` from `users` where `location` = ? and `is_admin` = ? and `id` > ?";
        $statement = User::select('id', 'username')->where('location', '=', 'us')->where('is_admin', 1)->whereId('>', 30)->toSelectSql();
        $this->assertEquals($expected, $statement);
    }

    /** @test */
    public function it_can_convert_all_columns()
    {
        $expected = "select * from `users` where `location` = ? and `is_admin` = ? and `id` > ?";
        $statement = User::where('location', '=', 'us')->where('is_admin', 1)->whereId('>', 30)->toSelectSql();
        $this->assertEquals($expected, $statement);
    }

    /** @test */
    public function it_can_convert_no_wheres()
    {
        $expected = "select * from `users`";
        $statement = User::toSelectSql();
        $this->assertEquals($expected, $statement);
    }

    /** @test */
    public function it_can_convert_with_one_where()
    {
        $expected = "select * from `users` where `id` = ?";
        $statement = User::whereId(30)->toSelectSql();
        $this->assertEquals($expected, $statement);
    }

    /** @test */
    public function it_can_convert_where_in()
    {
        $expected = "select * from `users` where `id` in (?,?)";
        $statement = User::whereIn('id', [30, 40])->toSelectSql();
        $this->assertEquals($expected, $statement);
    }

    /** @test */
    public function it_can_convert_where_in_with_where()
    {
        $expected = "select * from `users` where `id` in (?,?) and `is_admin` = ?";
        $statement = User::whereIn('id', [30, 40])->where('is_admin', 1)->toSelectSql();
        $this->assertEquals($expected, $statement);

        $expected = "select * from `users` where `is_admin` = ? and `id` in (?,?)";
        $statement = User::where('is_admin', 1)->whereIn('id', [30, 40])->toSelectSql();
        $this->assertEquals($expected, $statement);
    }

    /** @test */
    public function it_can_convert_where_not_in_with_where()
    {
        $expected = "select * from `users` where `id` not in (?,?) and `is_admin` = ?";
        $statement = User::whereNotIn('id', [30, 40])->where('is_admin', 1)->toSelectSql();
        $this->assertEquals($expected, $statement);

        $expected = "select * from `users` where `is_admin` = ? and `id` not in (?,?)";
        $statement = User::where('is_admin', 1)->whereNotIn('id', [30, 40])->toSelectSql();
        $this->assertEquals($expected, $statement);
    }

    /** @test */
    public function it_can_convert_where_between()
    {
        $expected = "select * from `users` where `id` between ? and ?";
        $statement = User::whereBetween('id', [30, 40])->toSelectSql();
        $this->assertEquals($expected, $statement);
    }

    /** @test */
    public function it_can_convert_where_not_between()
    {
        $expected = "select * from `users` where `id` not between ? and ?";
        $statement = User::whereNotBetween('id', [30, 40])->toSelectSql();
        $this->assertEquals($expected, $statement);
    }

    /** @test */
    public function it_can_convert_where_between_with_wheres()
    {
        $expected = "select * from `users` where `id` between ? and ? and `is_admin` = ?";
        $statement = User::whereBetween('id', [30, 40])->where('is_admin', 1)->toSelectSql();
        $this->assertEquals($expected, $statement);

        $expected = "select * from `users` where `is_admin` = ? and `id` between ? and ?";
        $statement = User::where('is_admin', 1)->whereBetween('id', [30, 40])->toSelectSql();
        $this->assertEquals($expected, $statement);
    }

    /** @test */
    public function it_can_convert_where_not_between_with_wheres()
    {
        $expected = "select * from `users` where `id` not between ? and ? and `is_admin` = ?";
        $statement = User::whereNotBetween('id', [30, 40])->where('is_admin', 1)->toSelectSql();
        $this->assertEquals($expected, $statement);

        $expected = "select * from `users` where `is_admin` = ? and `id` not between ? and ?";
        $statement = User::where('is_admin', 1)->whereNotBetween('id', [30, 40])->toSelectSql();
        $this->assertEquals($expected, $statement);
    }

    /** @test */
    public function it_can_convert_where_null()
    {
        $expected = "select * from `users` where `id` is null";
        $statement = User::whereNull('id')->toSelectSql();
        $this->assertEquals($expected, $statement);
    }

    /** @test */
    public function it_can_convert_where_not_null()
    {
        $expected = "select * from `users` where `id` is not null";
        $statement = User::whereNotNull('id')->toSelectSql();
        $this->assertEquals($expected, $statement);
    }

    /** @test */
    public function it_can_convert_where_null_with_wheres()
    {
        $expected = "select * from `users` where `id` is null and `is_admin` = ?";
        $statement = User::whereNull('id')->where('is_admin', 1)->toSelectSql();
        $this->assertEquals($expected, $statement);

        $expected = "select * from `users` where `is_admin` = ? and `id` is null";
        $statement = User::where('is_admin', 1)->whereNull('id')->toSelectSql();
        $this->assertEquals($expected, $statement);
    }

    /** @test */
    public function it_can_convert_where_not_null_with_wheres()
    {
        $expected = "select * from `users` where `id` is not null and `is_admin` = ?";
        $statement = User::whereNotNull('id')->where('is_admin', 1)->toSelectSql();
        $this->assertEquals($expected, $statement);

        $expected = "select * from `users` where `is_admin` = ? and `id` is not null";
        $statement = User::where('is_admin', 1)->whereNotNull('id')->toSelectSql();
        $this->assertEquals($expected, $statement);
    }
}