const CommentAdapter = (comment) => {
    return {
        id: comment.id,
        author: comment.name,
        project: comment.title,
        text: comment.comment,
        published_at: comment.created_at,
        moderated: comment.moderated ? false : true,
    };
};

export default CommentAdapter;
