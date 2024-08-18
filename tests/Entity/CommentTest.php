<?php

namespace App\Tests\Entity;

use App\Entity\Comment;
use App\Entity\Conference;
use PHPUnit\Framework\TestCase;
use DateTimeImmutable;

class CommentTest extends TestCase
{
    public function testGettersAndSetters(): void
    {
        $comment = new Comment();
        $conference = new Conference();

        $comment->setAuthor('John Doe');
        $comment->setText('This is a test comment');
        $comment->setEmail('john@example.com');
        $comment->setCreatedAt(new DateTimeImmutable());
        $comment->setConference($conference);
        $comment->setPhotoFilename('photo.jpg');

        $this->assertEquals('John Doe', $comment->getAuthor());
        $this->assertEquals('This is a test comment', $comment->getText());
        $this->assertEquals('john@example.com', $comment->getEmail());
        $this->assertInstanceOf(DateTimeImmutable::class, $comment->getCreatedAt());
        $this->assertEquals($conference, $comment->getConference());
        $this->assertEquals('photo.jpg', $comment->getPhotoFilename());
    }

    public function testSetCreatedAtValue(): void
    {
        $comment = new Comment();
        $comment->setCreatedAtValue();

        $this->assertInstanceOf(DateTimeImmutable::class, $comment->getCreatedAt());
    }
}
