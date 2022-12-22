<?php

namespace Andileong\Framework\Core\tests\Database\Model;

use Andileong\Framework\Core\Database\Model\Paginator;
use Andileong\Framework\Core\Request\Request;
use Andileong\Framework\Core\tests\Testcase\ApplicationTestCase;

class GeneratePaginatorLinksTest extends ApplicationTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $request = Request::setTest([], [], [
            'REQUEST_URI' => '/user',
            'HTTP_HOST' => 'localhost:8080',
        ], []);

        $this->fake('request', $request);
    }

    /** @test */
    public function it_can_correct_phase_first_page()
    {
        $links = $this->generatedLinks(1);
        $expectedLinks = [
            $this->link(1, 1, true),
            $this->link(2, 2, false),
            $this->link(3, 3, false),
            $this->link(4, '...', false),
            $this->link(10, 10, false),
        ];

        $this->assertEquals($expectedLinks, $links);
    }

    /** @test */
    public function it_can_correct_phase_second_page()
    {
        $links = $this->generatedLinks(2);
        $expectedLinks = [
            $this->link(1, 1, false),
            $this->link(2, 2, true),
            $this->link(3, 3, false),
            $this->link(4, '...', false),
            $this->link(10, 10, false),
        ];

        $this->assertEquals($expectedLinks, $links);
    }

    /** @test */
    public function it_can_correct_phase_third_page()
    {
        $links = $this->generatedLinks(3);
        $expectedLinks = [
            $this->link(1, 1, false),
            $this->link(2, 2, false),
            $this->link(3, 3, true),
            $this->link(4, '...', false),
            $this->link(10, 10, false),
        ];

        $this->assertEquals($expectedLinks, $links);
    }

    /** @test */
    public function it_can_correct_phase_forth_page()
    {
        $links = $this->generatedLinks(4);
        $expectedLinks = [
            $this->link(1, 1, false),
            $this->link(3, '...', false),
            $this->link(4, 4, true),
            $this->link(5, 5, false),
            $this->link(6, 6, false),
            $this->link(7, '...', false),
            $this->link(10, 10, false),
        ];

        $this->assertEquals($expectedLinks, $links);
    }

    /** @test */
    public function it_can_correct_phase_fifth_page()
    {
        $links = $this->generatedLinks(5);
        $expectedLinks = [
            $this->link(1, 1, false),
            $this->link(3, '...', false),
            $this->link(4, 4, false),
            $this->link(5, 5, true),
            $this->link(6, 6, false),
            $this->link(7, '...', false),
            $this->link(10, 10, false),
        ];

        $this->assertEquals($expectedLinks, $links);
    }

    /** @test */
    public function it_can_correct_phase_sixth_page()
    {
        $links = $this->generatedLinks(6);
        $expectedLinks = [
            $this->link(1, 1, false),
            $this->link(3, '...', false),
            $this->link(4, 4, false),
            $this->link(5, 5, false),
            $this->link(6, 6, true),
            $this->link(7, '...', false),
            $this->link(10, 10, false),
        ];

        $this->assertEquals($expectedLinks, $links);
    }

    /** @test */
    public function it_can_correct_phase_seventh_page()
    {
        $links = $this->generatedLinks(7);
        $expectedLinks = [
            $this->link(1, 1, false),
            $this->link(6, '...', false),
            $this->link(7, 7, true),
            $this->link(8, 8, false),
            $this->link(9, 9, false),
            $this->link(10, '...', false),
            $this->link(10, 10, false),
        ];

        $this->assertEquals($expectedLinks, $links);
    }

    /** @test */
    public function it_can_correct_phase_eighth_page()
    {
        $links = $this->generatedLinks(8);
        $expectedLinks = [
            $this->link(1, 1, false),
            $this->link(6, '...', false),
            $this->link(7, 7, false),
            $this->link(8, 8, true),
            $this->link(9, 9, false),
            $this->link(10, '...', false),
            $this->link(10, 10, false),
        ];

        $this->assertEquals($expectedLinks, $links);
    }

    /** @test */
    public function it_can_correct_phase_ninth_page()
    {
        $links = $this->generatedLinks(9);
        $expectedLinks = [
            $this->link(1, 1, false),
            $this->link(6, '...', false),
            $this->link(7, 7, false),
            $this->link(8, 8, false),
            $this->link(9, 9, true),
            $this->link(10, '...', false),
            $this->link(10, 10, false),
        ];

        $this->assertEquals($expectedLinks, $links);
    }

    /** @test */
    public function it_can_correct_phase_tenth_page()
    {
        $links = $this->generatedLinks(10);
        $expectedLinks = [
            $this->link(1, 1, false),
            $this->link(9, '...', false),
            $this->link(10, 10, true),
        ];

        $this->assertEquals($expectedLinks, $links);
    }

    public function generatedLinks($currentPage)
    {
        $paginator = new Paginator([], 10, 100, $currentPage, 'page');
        return json_decode($paginator->toJson(), true)['links'];
    }

    public function link($page, $label, $active)
    {
        return [
            'url' => $this->url($page),
            'label' => $label,
            'page' => $page,
            'active' => $active,
        ];
    }

    public function url($page = null)
    {
        $query = http_build_query(['page' => $page]);
        return 'http://localhost:8080/user?' . $query;
    }
}
