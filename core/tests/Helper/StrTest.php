<?php

namespace Andileong\Framework\Core\tests\Helper;

use Andileong\Framework\Core\Support\Str;
use PHPUnit\Framework\TestCase;

class StrTest extends testcase
{

    /** @test */
    public function it_can_generate_random_string()
    {
        $str = Str::random();
        $str2 = Str::random(16);
        $str3 = Str::random(3000);
        $str4 = Str::random(3000000);
        $this->assertEquals(10, strlen($str));
        $this->assertEquals(16, strlen($str2));
        $this->assertEquals(3000, strlen($str3));
        $this->assertEquals(3000000, strlen($str4));
    }

    /** @test */
    public function it_can_convert_to_camel_case()
    {
        $str = Str::camel('process_payment');
        $str2 = Str::camel('Process_Payment');
        $str3 = Str::camel('Process@Payment', '@');
        $this->assertEquals('ProcessPayment', $str);
        $this->assertEquals('ProcessPayment', $str2);
        $this->assertEquals('ProcessPayment', $str3);
    }

    /** @test */
    public function it_can_convert_to_kebab_case()
    {
        $str = Str::kebab('processPayment');
        $str2 = Str::kebab('ProcessPayment');
        $str3 = Str::kebab('Process@Payment');
        $str4 = Str::kebab('ProcessPayMent');
        $str5 = Str::kebab('P rocessPayMent');
        $this->assertEquals('process-payment', $str);
        $this->assertEquals('process-payment', $str2);
        $this->assertEquals('process@-payment', $str3);
        $this->assertEquals('process-pay-ment', $str4);
        $this->assertEquals('process-pay-ment', $str5);
    }

    /** @test */
    public function it_can_convert_to_snake_case()
    {
        $str = Str::snake('processPayment');
        $str2 = Str::snake('ProcessPayment');
        $str3 = Str::snake('Process@Payment');
        $str4 = Str::snake('ProcessPayMent');
        $str5 = Str::snake('P rocessPayMent');
        $this->assertEquals('process_payment', $str);
        $this->assertEquals('process_payment', $str2);
        $this->assertEquals('process@_payment', $str3);
        $this->assertEquals('process_pay_ment', $str4);
        $this->assertEquals('process_pay_ment', $str5);
    }

    /** @test */
    public function it_can_get_a_portion_of_string_before_a_string()
    {
        $str = Str::before('User@create', '@');
        $str2 = Str::before('User@create', '|');
        $str3 = Str::before('User@create', '');
        $str4 = Str::before('User @create', ' ');
        $str5 = Str::before(' User @create', ' ');
        $str6 = Str::before('你好吗', '好');
        $this->assertEquals('User', $str);
        $this->assertEquals('User@create', $str2);
        $this->assertEquals('User@create', $str3);
        $this->assertEquals('User', $str4);
        $this->assertEquals('', $str5);
        $this->assertEquals('你', $str6);
    }

    /** @test */
    public function it_can_get_a_portion_of_string_after_a_string()
    {
        $str = Str::after('User@create', '@');
        $str2 = Str::after('User@create', '|');
        $str3 = Str::after('User@create', '');
        $str5 = Str::after('User@create', 'User@');
        $str6 = Str::after(' User @create', ' ');
        $str7 = Str::after('User @create', ' ');
        $this->assertEquals('create', $str);
        $this->assertEquals('User@create', $str2);
        $this->assertEquals('User@create', $str3);
        $this->assertEquals('create', $str5);
        $this->assertEquals('User @create', $str6);
        $this->assertEquals('@create', $str7);
    }

    /** @test */
    public function it_can_get_a_portion_of_string_between_a_string()
    {
        $str = Str::between('User@create', 'User', 'create');
        $str2 = Str::between('this is password, please remember', 'this is ', ', please remember');
        $str3 = Str::between('User@create', 'xx', 'create');
        $str4 = Str::between('User@create', 'User', 'yyy');
        $str5 = Str::between('User @ create', 'User ', ' create');
        $this->assertEquals('@', $str);
        $this->assertEquals('password', $str2);
        $this->assertEquals('User@create', $str3);
        $this->assertEquals('User@create', $str4);
        $this->assertEquals('@', $str5);
    }

    /** @test */
    public function it_can_remove_all_occurrence_of_the_string()
    {
        $string = 'User@create';
        $str = Str::remove($string, 'User');
        $str2 = Str::remove($string, 'e');
        $str3 = Str::remove($string, '|');
        $this->assertEquals('@create', $str);
        $this->assertEquals('Usr@crat', $str2);
        $this->assertEquals('User@create', $str3);
    }

    /** @test */
    public function it_can_remove_first_occurrence_of_the_search_of_the_string()
    {
        $string = 'User@create';
        $str = Str::removeFirst($string, 'User');
        $str2 = Str::removeFirst($string, 'e');
        $str3 = Str::removeFirst($string, '|');
        $str4 = Str::removeFirst($string, ' ');
        $this->assertEquals('@create', $str);
        $this->assertEquals('Usr@create', $str2);
        $this->assertEquals('User@create', $str3);
        $this->assertEquals('User@create', $str4);
    }

    /** @test */
    public function it_can_replace_all_occurrence_of_the_string()
    {
        $string = 'User@create';
        $str = Str::replace($string, 'User', 'John');
        $str2 = Str::replace($string, 'e', 'z');
        $str3 = Str::replace($string, '|');
        $this->assertEquals('John@create', $str);
        $this->assertEquals('Uszr@crzatz', $str2);
        $this->assertEquals('User@create', $str3);
    }

    /** @test */
    public function it_can_replace_first_occurrence_of_the_search_of_the_string()
    {
        $string = 'User@create';
        $str = Str::replaceFirst($string, '@', '|');
        $str2 = Str::replaceFirst($string, 'e', 'z');
        $str3 = Str::replaceFirst($string, '|', '@');
        $str4 = Str::replaceFirst($string, ' ', 'space');
        $str5 = Str::replaceFirst($string, 'u', 'A');
        $str6 = Str::replaceFirst($string, 'U', 'A');
        $str7 = Str::replaceFirst($string, '', 'A');
        $str8 = Str::replaceFirst('User@ create', ' ', 'A');

        $this->assertEquals('User|create', $str);
        $this->assertEquals('Uszr@create', $str2);
        $this->assertEquals('User@create', $str3);
        $this->assertEquals('User@create', $str4);
        $this->assertEquals('User@create', $str5);
        $this->assertEquals('Aser@create', $str6);
        $this->assertEquals('User@create', $str7);
        $this->assertEquals('User@Acreate', $str8);
    }

    /** @test */
    public function it_can_separate_a_string_using_substr_driver()
    {
        $separator = '-';
        $uuid = '123e4567e89b12d3a456426614174000';
        $res1 = Str::separate($uuid, $separator, [8, 4, 4, 4, 12]);
        $res2 = Str::separate($uuid, $separator, [8, 4, 4, 4, 10]);
        $res3 = Str::separate($uuid, $separator, [8, 4, 4, 4, 13]);
        $res4 = Str::separate($uuid, '@', [8, 4, 4, 4, 13]);
        $res5 = Str::separate('abcdefg', '|');

        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000', $res1);
        $this->assertEquals('123e4567-e89b-12d3-a456-4266141740-00', $res2);
        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000', $res3);
        $this->assertEquals('123e4567@e89b@12d3@a456@426614174000', $res4);
        $this->assertEquals('a|b|c|d|e|f|g', $res5);
    }

    /** @test */
    public function it_can_separate_a_string_using_array_driver()
    {
        $separator = '-';
        $uuid = '123e4567e89b12d3a456426614174000';
        $res1 = Str::separate($uuid, $separator, [8, 4, 4, 4, 12], 'array');
        $res2 = Str::separate($uuid, $separator, [8, 4, 4, 4, 10], 'array');
        $res3 = Str::separate($uuid, $separator, [8, 4, 4, 4, 13], 'array');
        $res4 = Str::separate($uuid, '@', [8, 4, 4, 4, 13], 'array');
        $res5 = Str::separate('abcdefg', '|', [], 'array');

        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000', $res1);
        $this->assertEquals('123e4567-e89b-12d3-a456-4266141740-00', $res2);
        $this->assertEquals('123e4567-e89b-12d3-a456-426614174000', $res3);
        $this->assertEquals('123e4567@e89b@12d3@a456@426614174000', $res4);
        $this->assertEquals('a|b|c|d|e|f|g', $res5);
    }

    /** @test */
    public function it_can_limit_a_string()
    {
        $string = 'Johnny Johnny, yes papa, eating sugar? no papa. telling lies no papa';
        $str = Str::limit($string, 23);
        $str2 = Str::limit($string, 2000);
        $str3 = Str::limit($string, 13, '.');
        $str4 = Str::limit('您好，我很帅', 2);
        $this->assertEquals('Johnny Johnny, yes papa...', $str);
        $this->assertEquals($string . '...', $str2);
        $this->assertEquals('Johnny Johnny.', $str3);
        $this->assertEquals('您好...', $str4);
    }

    /** @test */
    public function it_can_mask_a_string_from_positive_index()
    {
        $string = 'helloword@gmail.com';
        $normalSuccessCase = Str::mask($string, '*', 5);
        $indexIsTooLarge = Str::mask($string, '*', 500);
        $indexEqualsTotalLength = Str::mask('abcd', '|', 4);
        $indexEqualsTotalLengthWithLength = Str::mask('abcd', '*', 4, 1);
        $indexIsZero = Str::mask('abcd', '|', 0);
        $replacementIsEmpty = Str::mask($string, '', 4);
        $successWithSpecifyLength = Str::mask($string, '*', 5, 4);
        $lengthIsTooLarge = Str::mask($string, '*', 5, 100);

        $this->assertEquals('hello**************', $normalSuccessCase);
        $this->assertEquals($string, $indexIsTooLarge);
        $this->assertEquals('abcd', $indexEqualsTotalLength);
        $this->assertEquals('abcd', $indexIsZero);
        $this->assertEquals($string, $replacementIsEmpty);
        $this->assertEquals('hello****@gmail.com', $successWithSpecifyLength);
        $this->assertEquals('hello**************', $lengthIsTooLarge);
        $this->assertEquals('abcd', $indexEqualsTotalLengthWithLength);
    }

    /** @test */
    public function it_can_mask_a_string_from_negative_index()
    {
        $string = 'helloword@gmail.com';
        $normalSuccessCase = Str::mask($string, '*', -14, 4);
        $lengthIsOmitted = Str::mask($string, '*', -14);
        $lengthIsTooLarge = Str::mask($string, '*', -14, 100);
        $indexIsTooLarge = Str::mask($string, '*', -14000, 4);
        $indexLengthSameAsStringLength = Str::mask($string, '*', -19, 4);

        $this->assertEquals('hello****@gmail.com', $normalSuccessCase);
        $this->assertEquals('hello**************', $lengthIsOmitted);
        $this->assertEquals('hello**************', $lengthIsTooLarge);
        $this->assertEquals($string, $indexIsTooLarge);
        $this->assertEquals('****oword@gmail.com', $indexLengthSameAsStringLength);
    }

    /** @test */
    public function it_can_revert_a_string()
    {
        $string = 'hello';
        $str = Str::reverse($string);
        $str2 = Str::reverse('hello/n');
        $str3 = Str::reverse('');
        $str4 = Str::reverse('你好');
        $this->assertEquals('olleh', $str);
        $this->assertEquals('n/olleh', $str2);
        $this->assertEquals('', $str3);
        $this->assertEquals('好你', $str4);
    }

    /** @test */
    public function it_can_make_string_title()
    {
        $string = 'hello world, I am handsome';
        $str = Str::title($string);
        $str2 = Str::title('Xcvx awsdsd@sd sdfdf');
        $this->assertEquals('Hello World, I Am Handsome', $str);
        $this->assertEquals('Xcvx Awsdsd@Sd Sdfdf', $str2);
    }

    /** @test */
    public function it_can_calculate_words_of_a_string()
    {
        $string = 'hello world, I am handsome';
        $str = Str::wordCount($string);
        $this->assertEquals(5, $str);
    }

    /** @test */
    public function it_can_generate_a_uuid4()
    {
        $uuid = Str::uuid4();
        $this->assertTrue(Str::isUuid($uuid));
    }

    /** @test */
    public function it_can_generate_a_ulid()
    {
        $uuid = Str::ulid();
        $this->assertTrue(strlen($uuid) === 26);
    }

    /** @test */
    public function it_can_validate_ulid()
    {
        $this->assertTrue(Str::isUlid(Str::ulid()));
        $this->assertTrue(Str::isUlid(Str::ulid(false)));
        $this->assertTrue(Str::isUlid('ABCDEFGHJKMNPQRSTVWXYZ0123'));

        // not exactly 26 long
        $this->assertFalse(Str::isUlid('abcdefghjkmnpqrstvwxyz01235'));

        // contains invalid letter
        $this->assertFalse(Str::isUlid('i'));
        $this->assertFalse(Str::isUlid('i0123abcdefghjkmnpqrstvwxyz'));
        $this->assertFalse(Str::isUlid('+@|'));
    }
}