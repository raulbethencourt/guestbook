<?php

namespace App\Tests;

use App\Entity\Comment;
use App\SpamChecker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Generator;

/**
 * This class represents a test case for the SpamChecker functionality.
 *
 * The SpamChecker is responsible for determining whether a given comment is spam or not by
 * communicating with the Akismet API. This test case includes several test methods to ensure
 * the correct behavior of the SpamChecker under different scenarios.
 *
 * The test methods cover the following scenarios:
 *
 * 1. testSpamScoreWithInvalidRequest: Tests the behavior when the Akismet API returns an invalid
 *    response, such as an "Invalid key" error. It expects a RuntimeException to be thrown with
 *    a specific error message.
 *
 * 2. testSpamScore: Tests the SpamChecker's ability to correctly determine the spam score for
 *    various scenarios, including blatant spam, spam, and ham (non-spam). This test method uses
 *    a data provider to test different combinations of expected spam scores, mock API responses,
 *    comments, and contexts.
 *
 * 3. provideComments: A data provider method for the testSpamScore method. It returns an iterable
 *    of test cases, each containing an expected spam score, a mock API response, a comment object,
 *    and a context array.
 *
 * This test case utilizes the MockHttpClient from the Symfony HttpClient component to simulate
 * different API responses. It also creates instances of the Comment entity and the SpamChecker
 * class for testing purposes.
 */
class SpamCheckerTest extends TestCase
{
    /**
     * Tests the behavior of the SpamChecker when the Akismet API returns an invalid response.
     *
     * This test case creates a mock HTTP client that returns an invalid response with an "Invalid key"
     * error message. It then creates a SpamChecker instance with the mock client and an invalid API key.
     * The test expects a RuntimeException to be thrown with a specific error message when trying to
     * check for spam with an invalid request.
     *
     * @return void
     *
     * @throws RuntimeException If the expected exception is not thrown.
     */
    public function testSpamScoreWithInvalidRequest(): void
    {
        $comment = new Comment();
        $comment->setCreatedAtValue();
        $context = [];

        $client = new MockHttpClient([
            new MockResponse('invalid', [
                'response_headers' => [
                    'x-akismet-debug-help: Invalid key'
                ]
            ]),
        ]);
        $checker = new SpamChecker($client, 'abcde');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Unable to check for spam: invalid (Invalid key).');
        $checker->getSpamScore($comment, $context);
    }

    /**
     * Tests the SpamChecker's ability to correctly determine the spam score for various scenarios.
     *
     * This test method uses a data provider to test different combinations of expected spam scores,
     * mock API responses, comments, and contexts. It creates a SpamChecker instance with a mock HTTP
     * client that returns the provided mock response. The test then calls the getSpamScore method
     * with the provided comment and context, and asserts that the returned score matches the expected
     * score.
     *
     * @dataProvider provideComments
     *
     * @param int $expectedScore The expected spam score for the given scenario.
     * @param ResponseInterface $response The mock API response to be returned by the HTTP client.
     * @param Comment $comment The comment object to be checked for spam.
     * @param array $context The context data associated with the comment.
     *
     * @return void
     */
    public function testSpamScore(
        int $expectedScore,
        ResponseInterface $response,
        Comment $comment,
        array $context
    ): void {
        $client = new MockHttpClient([$response]);
        $checker = new SpamChecker($client, 'abcde');

        $score = $checker->getSpamScore($comment, $context);
        $this->assertSame($expectedScore, $score);
    }

    /**
     * Data provider for the testSpamScore method.
     *
     * This method returns an iterable of test cases, each containing an expected spam score, a mock
     * API response, a comment object, and a context array. The test cases cover different scenarios,
     * such as blatant spam, spam, and ham (non-spam).
     *
     * @return Generator<string, array{int, Symfony\Component\HttpClient\Response\MockResponse, App\Entity\Comment, array}>
     *         An iterable of test cases, where each case is an array containing the expected spam score,
     *         a mock API response, a comment object, and a context array.
     */
    public function provideComments(): iterable
    {
        $comment = new Comment();
        $comment->setCreatedAtValue();
        $context = [];

        $response = new MockResponse('', ['response_headers' => [
            'x-akismet-pro-tip: discard'
        ]]);
        yield 'blatant_spam' => [2, $response, $comment, $context];

        $response = new MockResponse('true');
        yield 'spam' => [1, $response, $comment, $context];

        $response = new MockResponse('false');
        yield 'ham' => [0, $response, $comment, $context];
    }
}
