<?php

namespace Filament\Tables\Testing;

use Closure;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Testing\Assert;
use Livewire\Testing\TestableLivewire;

/**
 * @method HasTable instance()
 *
 * @mixin TestableLivewire
 */
class TestsSummaries
{
    public function assertTableColumnSummarySet(): Closure
    {
        return function (string $columnName, string $summarizerId, $state, bool $isCurrentPaginationPageOnly = false): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnSummarizerExists($columnName, $summarizerId);

            $normalizeState = fn ($state): string => strval(
                (is_float($state) || is_int($state)) ? round($state, 5) : $state,
            );

            $state = is_array($state) ?
                array_map($normalizeState, $state) :
                $normalizeState($state);

            $summarizer = $this->instance()->getTable()->getColumn($columnName)->getSummarizer($summarizerId);

            $query = $isCurrentPaginationPageOnly ?
                $this->instance()->getPageTableSummaryQuery() :
                $this->instance()->getAllTableSummaryQuery();

            $actualState = $summarizer->query($query)->getState();

            $actualState = is_array($actualState) ?
                array_map($normalizeState, $actualState) :
                $normalizeState($actualState);

            $livewireClass = $this->instance()::class;

            Assert::assertSame(
                $state,
                $actualState,
                message: "Failed asserting that summarizer [$summarizerId], for column [{$columnName}], on the [{$livewireClass}] component, is set.",
            );

            return $this;
        };
    }

    public function assertTableColumnSummaryNotSet(): Closure
    {
        return function (string $columnName, string $summarizerId, $state, bool $isCurrentPaginationPageOnly = false): static {
            /** @phpstan-ignore-next-line */
            $this->assertTableColumnSummarizerExists($columnName, $summarizerId);

            $normalizeState = fn ($state): string => strval(
                (is_float($state) || is_int($state)) ? round($state, 5) : $state,
            );

            $state = is_array($state) ?
                array_map($normalizeState, $state) :
                $normalizeState($state);

            $summarizer = $this->instance()->getTable()->getColumn($columnName)->getSummarizer($summarizerId);

            $query = $isCurrentPaginationPageOnly ?
                $this->instance()->getPageTableSummaryQuery() :
                $this->instance()->getAllTableSummaryQuery();

            $actualState = $summarizer->query($query)->getState();

            $actualState = is_array($actualState) ?
                array_map($normalizeState, $actualState) :
                $normalizeState($actualState);

            $livewireClass = $this->instance()::class;

            Assert::assertNotSame(
                $state,
                $actualState,
                message: "Failed asserting that summarizer [$summarizerId], for column [{$columnName}], on the [{$livewireClass}] component, is not set.",
            );

            return $this;
        };
    }

    public function assertTableColumnSummarizerExists(): Closure
    {
        return function (string $columnName, string $summarizerId): static {
            $this->assertTableColumnExists($columnName);

            $column = $this->instance()->getTable()->getColumn($columnName);

            $summarizer = $column->getSummarizer($summarizerId);

            $livewireClass = $this->instance()::class;

            Assert::assertInstanceOf(
                Summarizer::class,
                $summarizer,
                message: "Failed asserting that a table column with name [{$columnName}] has a summarizer with ID [{$summarizerId}] on the [{$livewireClass}] component. Please ensure that the ID is passed to the summarizer with [Summarizer::make('{$summarizerId}')].",
            );

            return $this;
        };
    }
}