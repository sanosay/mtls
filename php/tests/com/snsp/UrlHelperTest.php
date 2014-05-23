<?php
namespace tests\com\snsp;
/**
 *  Unit Tests for UrlHelper 
 */
class UrlHelperTest extends \PHPUnit_Framework_TestCase
{
    private static $URLS = [
        'https://test:asd@www.google.com:80/asd/asdf/',
        'https://test:asd@www.google.com:80/asd/asdf/?sss#asdddd',
        'https://te   st:asd@www.g  oogle.com:80/asd/asdf/?sss#asdddd',
        'www.google.com:80',
        //'www.google.com',  <-- bug in original parse_url
        '/ehlo/test?something=0',
        '/ehlo/test?something=0#fragmentPart',
        //'www.example.tt:8080?param=Test', <--  bug in original parse_url
        
    ];
    /**
     * Test all URL components against native php 'parse_url' function
     * @covers com\snsp\UrlHelper::ParseUrl 
     */
    public function testParseUrlAllComponents()
    {
        
        for($i=0;$i<count(static::$URLS);$i++){
            $actual = \com\snsp\UrlHelper::ParseUrl(static::$URLS[$i]);
            $expected = parse_url(static::$URLS[$i]);
            ksort($actual);
            ksort($expected);
            print_r($actual);
            $this->assertSame($expected,$actual);
        }
     
        
    }
    
    
    /**
     *  Test Scheme extraction from a url
     *  @covers com\snsp\UrlHelper::ExtractScheme 
     */
    public function testExtractScheme(){
        $url = 'http://www.google.com/';
        $actual = \com\snsp\UrlHelper::ExtractScheme($url);
        $this->assertEquals('http',$actual['scheme']);
        $this->assertEquals('www.google.com/',$actual['remain']);
        
        $url = 'www.google.com/';
        $actual = \com\snsp\UrlHelper::ExtractScheme($url);
        $this->assertEquals(null,$actual['scheme']);
        $this->assertEquals('www.google.com/',$actual['remain']);
        
        
        $url = 'www.example.tt:8080?param=Test';
        $actual = \com\snsp\UrlHelper::ExtractScheme($url);
        $this->assertEquals(null,$actual['scheme']);
        $this->assertEquals('www.example.tt:8080?param=Test',$actual['remain']);
    }
    
    /**
     *  Test username and password extraction from a url
     *  @covers com\snsp\UrlHelper::ExtractUsernamePassword 
     */
    public function testExtractUsernamePassword(){
        $url = 'test@www.google.com/';
        $actual = \com\snsp\UrlHelper::ExtractUsernamePassword($url);
        $this->assertEquals('test',$actual['username']);
        $this->assertEquals(null,$actual['password']);
        $this->assertEquals('www.google.com/',$actual['remain']);
        
        $url = 'test:xyz@www.google.com/';
        $actual = \com\snsp\UrlHelper::ExtractUsernamePassword($url);
        $this->assertEquals('test',$actual['username']);
        $this->assertEquals('xyz',$actual['password']);
        $this->assertEquals('www.google.com/',$actual['remain']);
        
        $url = 'www.google.com/';
        $actual = \com\snsp\UrlHelper::ExtractUsernamePassword($url);
        $this->assertEquals(null,$actual['username']);
        $this->assertEquals(null,$actual['password']);
        $this->assertEquals('www.google.com/',$actual['remain']);
    }
    
    /**
     *  Test Scheme extraction from a url
     *  @covers com\snsp\UrlHelper::ExtractUsernamePassword 
     */
    public function testExtractHostAndPort(){
        $url = 'www.google.com/';
        $actual = \com\snsp\UrlHelper::ExtractHostAndPort($url);
        $this->assertEquals('www.google.com',$actual['host']);
        $this->assertEquals(null,$actual['port']);
        $this->assertEquals('/',$actual['remain']);
        
        $url = 'www.google.com';
        $actual = \com\snsp\UrlHelper::ExtractHostAndPort($url);
        $this->assertEquals('www.google.com',$actual['host']);
        $this->assertEquals(null,$actual['port']);
        $this->assertEquals('',$actual['remain']);
        
        $url = 'www.google.com?asd=1';
        $actual = \com\snsp\UrlHelper::ExtractHostAndPort($url);
        $this->assertEquals('www.google.com',$actual['host']);
        $this->assertEquals(null,$actual['port']);
        $this->assertEquals('?asd=1',$actual['remain']);
        
       $url = 'www.google.com?asd=1#a';
        $actual = \com\snsp\UrlHelper::ExtractHostAndPort($url);
        $this->assertEquals('www.google.com',$actual['host']);
        $this->assertEquals(null,$actual['port']);
        $this->assertEquals('?asd=1#a',$actual['remain']);
        
        $url = 'www.google.com:80?asd=1#a';
        $actual = \com\snsp\UrlHelper::ExtractHostAndPort($url);
        $this->assertEquals('www.google.com',$actual['host']);
        $this->assertEquals(80,$actual['port']);
        $this->assertEquals('?asd=1#a',$actual['remain']);
        
        
        $url = 'localhost/a';
        $actual = \com\snsp\UrlHelper::ExtractHostAndPort($url,true);
        $this->assertEquals('localhost',$actual['host']);
        $this->assertEquals(null,$actual['port']);
        $this->assertEquals('/a',$actual['remain']);
       
    }
    
    /**
     *  Test Path extraction from a url
     *  @covers com\snsp\UrlHelper::ExtractPath
     */
    public function testExtractPath(){
        $url = '/test';
        $actual = \com\snsp\UrlHelper::ExtractPath($url);
        $this->assertEquals('/test',$actual['path']);
        $this->assertEquals('',$actual['remain']);
        
        $url = '/test/aasdf/';
        $actual = \com\snsp\UrlHelper::ExtractPath($url);
        $this->assertEquals('/test/aasdf/',$actual['path']);
        $this->assertEquals('',$actual['remain']);
        
        $url = '/test?a=1';
        $actual = \com\snsp\UrlHelper::ExtractPath($url);
        $this->assertEquals('/test',$actual['path']);
        $this->assertEquals('?a=1',$actual['remain']);
        
        $url = '/test?a=1#asd';
        $actual = \com\snsp\UrlHelper::ExtractPath($url);
        $this->assertEquals('/test',$actual['path']);
        $this->assertEquals('?a=1#asd',$actual['remain']);
        $url = '/test/?a=1#asd';
        
        $actual = \com\snsp\UrlHelper::ExtractPath($url);
        $this->assertEquals('/test/',$actual['path']);
        $this->assertEquals('?a=1#asd',$actual['remain']);

       
    }
    
    
    /**
     *  Test Query and Fragment extraction from a url
     *  @covers com\snsp\UrlHelper::ExtractQueryAndFragment
     */
    public function testExtractQueryAndFragment(){
        $url = '?asdf=1';
        $actual = \com\snsp\UrlHelper::ExtractQueryAndFragment($url);
        $this->assertEquals('asdf=1',$actual['query']);
        $this->assertEquals('',$actual['fragment']);
        $this->assertEquals('',$actual['remain']);
        
        $url = '?asdf=1#asd';
        $actual = \com\snsp\UrlHelper::ExtractQueryAndFragment($url);
        $this->assertEquals('asdf=1',$actual['query']);
        $this->assertEquals('asd',$actual['fragment']);
        $this->assertEquals('',$actual['remain']);
        
       

       
    }
}
