<?php

declare(strict_types=1);

namespace App\Tests\Unit\Quiz\Application\Query\GetQuiz;

use App\Quiz\Application\Query\GetQuiz\GetQuizHandler;
use App\Quiz\Application\Query\GetQuiz\GetQuizQuery;
use App\Quiz\Application\Query\GetQuiz\Model\QuizModel;
use App\Quiz\Domain\Question;
use App\Quiz\Domain\QuestionAnswer;
use App\Quiz\Domain\Quiz;
use App\Quiz\Domain\QuizConfiguration;
use App\Quiz\Domain\QuizQuestion;
use App\Quiz\Domain\Repository\QuizRepositoryInterface;
use App\Quiz\Domain\ValueObject\QuestionAnswerId;
use App\Quiz\Domain\ValueObject\QuizId;
use App\Quiz\Domain\ValueObject\QuizQuestionId;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(GetQuizHandler::class)]
final class GetQuizHandlerTest extends TestCase
{
    public function testReturnsModelWithCorrectId(): void
    {
        $quiz = $this->runInvokeOnInstance($this->createInstanceThatRepositoryReturns(
            $this->createQuizStub(
                id: $id = QuizId::create()
            )
        ));

        $this->assertSame($id, $quiz->id);
    }

    public function testReturnsModelWithCorrectConfiguration(): void
    {
        $quiz = $this->runInvokeOnInstance($this->createInstanceThatRepositoryReturns(
            $this->createQuizStub(
                configuration: $this->createQuizConfigurationStub(
                    duration: 47,
                    passingScore: 82
                )
            )
        ));

        $this->assertSame(47, $quiz->configuration->duration);
        $this->assertSame(82, $quiz->configuration->passingScore);
    }

    public function testReturnsModelWithCorrectQuestions(): void
    {
        $quiz = $this->runInvokeOnInstance($this->createInstanceThatRepositoryReturns(
            $this->createQuizStub(
                questions: [
                    $this->createQuizQuestionStub(
                        questionId: $question1Id = QuizQuestionId::create(),
                        content: 'What color is a lemon?',
                        answers: [
                            $this->createQuestionAnswerStub(
                                answerId: $question1Answer1Id = QuestionAnswerId::create(),
                                content: 'Orange'
                            ),
                            $this->createQuestionAnswerStub(
                                answerId: $question1Answer2Id = QuestionAnswerId::create(),
                                content: 'Yellow'
                            ),
                            $this->createQuestionAnswerStub(
                                answerId: $question1Answer3Id = QuestionAnswerId::create(),
                                content: 'Red'),
                            $this->createQuestionAnswerStub(
                                answerId: $question1Answer4Id = QuestionAnswerId::create(),
                                content: 'Black'
                            ),
                        ]
                    ),
                    $this->createQuizQuestionStub(
                        questionId: $question2Id = QuizQuestionId::create(),
                        content: 'Which symbol should be used to define a variable in PHP?',
                        answers: [
                            $this->createQuestionAnswerStub(
                                answerId: $question2Answer1Id = QuestionAnswerId::create(),
                                content: '$'
                            ),
                            $this->createQuestionAnswerStub(
                                answerId: $question2Answer2Id = QuestionAnswerId::create(),
                                content: '#'
                            ),
                        ]
                    ),
                ]
            )
        ));

        $question1 = $quiz->questions[0];
        $question2 = $quiz->questions[1];

        // ids
        $this->assertEquals($question1Id, $question1->id);
        $this->assertEquals($question2Id, $question2->id);

        // contents
        $this->assertEquals('What color is a lemon?', $question1->content);
        $this->assertEquals('Which symbol should be used to define a variable in PHP?', $question2->content);

        // answers ids
        $this->assertEquals($question1Answer1Id, $question1->answers[0]->id);
        $this->assertEquals($question1Answer2Id, $question1->answers[1]->id);
        $this->assertEquals($question1Answer3Id, $question1->answers[2]->id);
        $this->assertEquals($question1Answer4Id, $question1->answers[3]->id);

        $this->assertEquals($question2Answer1Id, $question2->answers[0]->id);
        $this->assertEquals($question2Answer2Id, $question2->answers[1]->id);

        // answers contents
        $this->assertEquals('Orange', $question1->answers[0]->content);
        $this->assertEquals('Yellow', $question1->answers[1]->content);
        $this->assertEquals('Red', $question1->answers[2]->content);
        $this->assertEquals('Black', $question1->answers[3]->content);

        $this->assertEquals('$', $question2->answers[0]->content);
        $this->assertEquals('#', $question2->answers[1]->content);
    }

    private function createInstanceThatRepositoryReturns(
        Quiz $quiz,
    ): GetQuizHandler {
        $repository = $this->createStub(QuizRepositoryInterface::class);
        $repository->method('get')->willReturn($quiz);

        return new GetQuizHandler($repository);
    }

    private function runInvokeOnInstance(GetQuizHandler $handler, ?QuizId $quizId = null): QuizModel
    {
        return $handler(new GetQuizQuery($quizId ?? QuizId::create()));
    }

    private function createQuizStub(
        ?QuizId $id = null,
        ?QuizConfiguration $configuration = null,
        ?array $questions = null,
    ): Quiz&Stub {
        $quiz = $this->createStub(Quiz::class);
        $quiz->method('getId')->willReturn($id ?? QuizId::create());
        $quiz->method('getConfiguration')->willReturn($configuration ?? $this->createStub(QuizConfiguration::class));
        $quiz->method('getQuestions')->willReturn($questions ?? [
            $this->createQuizQuestionStub(),
            $this->createQuizQuestionStub(),
            $this->createQuizQuestionStub(),
        ]);

        return $quiz;
    }

    private function createQuizConfigurationStub(
        int $duration = 50,
        int $passingScore = 65,
    ): QuizConfiguration&Stub {
        $configuration = $this->createStub(QuizConfiguration::class);
        $configuration->method('getDuration')->willReturn($duration);
        $configuration->method('getPassingScore')->willReturn($passingScore);

        return $configuration;
    }

    private function createQuizQuestionStub(
        ?QuizQuestionId $questionId = null,
        string $content = 'What color is a lemon?',
        ?array $answers = null,
    ): QuizQuestion&Stub {
        $quizQuestion = $this->createStub(QuizQuestion::class);
        $quizQuestion->method('getId')->willReturn($questionId ?? QuizQuestionId::create());

        $quizQuestion->method('getQuestion')->willReturn($question = $this->createStub(Question::class));

        $question->method('getContent')->willReturn($content);
        $question->method('getAnswers')->willReturn($answers ?? [
            $this->createQuestionAnswerStub(content: 'Orange'),
            $this->createQuestionAnswerStub(content: 'Yellow'),
            $this->createQuestionAnswerStub(content: 'Orange'),
            $this->createQuestionAnswerStub(content: 'Red'),
        ]);

        return $quizQuestion;
    }

    private function createQuestionAnswerStub(
        ?QuestionAnswerId $answerId = null,
        string $content = 'What color is a lemon?',
    ): QuestionAnswer&Stub {
        $answer = $this->createStub(QuestionAnswer::class);
        $answer->method('getId')->willReturn($answerId ?? QuestionAnswerId::create());
        $answer->method('getContent')->willReturn($content);

        return $answer;
    }
}
