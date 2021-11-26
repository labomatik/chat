<?php

namespace Musonza\Chat\Traits;

use Illuminate\Database\Eloquent\Relations\MorphMany;
use Musonza\Chat\Exceptions\InvalidDirectMessageNumberOfParticipants;
use Musonza\Chat\Models\Conversation;
use Musonza\Chat\Models\Participation;

trait Messageable
{

    public function conversations($withArchive = false)
    {
        return $this
            ->participation()
            ->with(['conversation' => function ($query) use ($withArchive) {
                if (true === $withArchive) {
                    $query = $query->withTrashed();
                }

                return $query->with('last_message');
            }])
            ->get()
            ->pluck('conversation');
    }

    /**
     * @return MorphMany
     */
    public function participation(): MorphMany
    {
        return $this->morphMany(Participation::class, 'messageable');
    }

    public function joinConversation(Conversation $conversation)
    {
        if ($conversation->isDirectMessage() && $conversation->participants()->count() == 2) {
            throw new InvalidDirectMessageNumberOfParticipants();
        }

        $participation = new Participation([
            'messageable_id'   => $this->getKey(),
            'messageable_type' => $this->getMorphClass(),
            'conversation_id'  => $conversation->getKey(),
        ]);

        $this->participation()->save($participation);
    }

    public function leaveConversation($conversationId)
    {
        $this->participation()->where([
            'messageable_id'   => $this->getKey(),
            'messageable_type' => $this->getMorphClass(),
            'conversation_id'  => $conversationId,
        ])->delete();
    }
}
