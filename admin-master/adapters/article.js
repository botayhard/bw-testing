const ArticleAdapter = (article) => {
    const authorFirstname = article.firstname;
    const authorLastname = article.lastname;
    return {
        id: article.id,
        title: article.title,
        subtitle: article.title,
        author: `${authorFirstname} ${authorLastname}`,
        created_at: article.created_at,
        updated_at: article.updated_at,
        views: article.views,
        blocks: article.blocks,
        isMain: article.is_main,
        order: article.order,
    };
};

export default ArticleAdapter;
