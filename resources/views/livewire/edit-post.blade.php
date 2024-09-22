<div>
    <form wire:submit.prevent="updatePost">
        <textarea wire:model.lazy="content" rows="4" class="form-control">{{ $content }}</textarea>
        <button type="submit" class="btn btn-primary mt-2">Update</button>
    </form>
</div>
