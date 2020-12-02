// NB: /api prefix is omitted here and has to be set via API_URL

/**
 * @api {get} /proposals
 * @apiName allProposals
 * @apiGroup Proposals
 * @apiDescription returns current paginate list of proposals
 */

/**
 * @api {get} /proposals/:proposal
 * @apiName getProposal
 * @apiGroup Proposals
 * @apiDescription returns one proposal from list of proposals
 */

/**
 * @api {post} /proposals/:proposal/delete
 * @apiName deleteProposal
 * @apiGroup Proposals
 * @apiDescription delete one proposal from list of proposals
 */

/**
 * @api {post} /proposals/create
 * @apiName createProposal
 * @apiGroup Proposals
 * @apiParams name, phone, email, description
 * @apiDescription create a proposal
 */

/**
 * @api {post} /login
 * @apiName login
 * @apiGroup Users
 * @apiDescription login user
 */

/**
 * @api {post} /logout
 * @apiName logout
 * @apiGroup Users
 * @apiDescription logout user
 */

/**
 * @api {post} /send_feedback
 * @apiName sendMail
 * @apiGroup mailer
 * @apiDescription send mail to user
 */

/**
 * @api {get} /proposals/all/count
 * @apiName proposalsCount
 * @apiGroup proposals
 * @apiDescription get count of proposals
 */

/**
 * @api {get} /user/authorize
 * @apiName getUser
 * @apiGroup users
 * @apiDescription check user is authorize
 */

/**
 * @api {get} /proposals/history/getFromProposal/:proposal
 * @apiName getHistory
 * @apiGroup history
 * @apiDescription get history from Proposal
 */
/**
 * @api {post} /comment/:comment/update
 * @apiName moderateComment
 * @apiGroup comments
 * @apiDescription moderate comment
 */
/**
 * @api {post} comment/:comment/delete
 * @apiName deleteComment
 * @apiGroup comments
 * @apiDescription delete comment
 */
/**
 * @api {get} /comment
 * @apiName getComments
 * @apiGroup comments
 * @apiDescription get comments with filtres in route query
 */
/**
 * @api {get} /article
 * @apiName getAllArticles
 * @apiGroup portfolio
 * @apiDescription get all portfolio
 */
/**
 * @api {post} /article/:article/delete
 * @apiName deleteArticle
 * @apiGroup portfolio
 * @apiDescription delete article
 */
/**
 * @api {get} /article/:article
 * @apiName getArticle
 * @apiGroup portfolio
 * @apiDescription get article from id
 */

/**
 * @api {post} /article
 * @apiName createArticle
 * @apiGroup article
 * @apiDescription create new article
 */

/**
 * @api {post} /article/:id/update
 * @apiName updateArticle
 * @apiGroup article
 * @apiDescription update article
 */

/**
 * @api {post} /proposals/history/createComment
 * @apiName createComment
 * @apiGroup history
 * @apiDescription create comment to proposal
 */

/**
 * @api {post} /metatag/create
 * @apiName createMeta
 * @apiGroup metatag
 * @apiDescription save metatags
 */

/**
 * @api {get} /metatag/get/:metatag
 * @apiName getMeta
 * @apiGroup metatag
 * @apiDescription get metatag
 */

/**
 * @api {get} /tag/all
 * @apiName getAllTags
 * @apiGroup tag
 * @apiDescription get all tags
 */

/**
 * @api {post} /tag/delete/:tag
 * @apiName deleteTag
 * @apiGroup tag
 * @apiDescription delete current tag
 */

/**
 * @api {post} /tag/store
 * @apiName addTag
 * @apiGroup tag
 * @apiDescription add new tag
 */

/**
 * @api {post} /tag/update/:tag
 * @apiName updateTag
 * @apiGroup tag
 * @apiDescription update current tag
 */

/**
 * @api {post} /article/tag/delete/:article
 * @apiName deleteAllTags
 * @apiGroup tag
 * @apiDescription delete all tags for current article
 */

/**
 * @api {post} /article/add/tag/:article
 * @apiName addTagToArticle
 * @apiGroup tag
 * @apiDescription add array of tags to article
 */
/**
 * @api {get} /article/tag/:article
 * @apiName getArticleTag
 * @apiGroup tag
 * @apiDescription get all tags which belongs to current article
 */
/**
 * @api {get} /users/all
 * @apiName getAllUsers
 * @apiGroup users
 * @apiDescription get all registered users
 */


// sorry for my english

