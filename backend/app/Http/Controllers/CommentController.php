<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Internship;
use App\Models\Garant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CommentController extends Controller
{
    /**
     * Display a listing of comments for a specific internship.
     *
     * @param int $internshipId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($internshipId)
    {
        try {
            // Find the internship
            $internship = Internship::findOrFail($internshipId);

            // Get comments with garant and user information
            $comments = Comment::where('internship_id', $internshipId)
                ->with(['garant.user'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Format the response
            $formattedComments = $comments->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'comment_type' => $comment->comment_type,
                    'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $comment->updated_at->format('Y-m-d H:i:s'),
                    'author' => [
                        'id' => $comment->garant->id,
                        'name' => $comment->garant->name,
                        'surname' => $comment->garant->surname,
                        'full_name' => $comment->garant->full_name,
                        'faculty' => $comment->garant->faculty,
                        'email' => $comment->garant->user->email ?? null,
                    ],
                ];
            });

            return response()->json([
                'message' => 'Comments retrieved successfully.',
                'data' => $formattedComments,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error retrieving comments for internship: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error retrieving comments.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created comment in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $internshipId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $internshipId)
    {
        try {
            // Validate the incoming request data
            $validated = $request->validate([
                'content' => [
                    'required',
                    'string',
                    'max:5000',
                ],
                'comment_type' => [
                    'required',
                    'string',
                    Rule::in(Comment::getTypes())
                ],
            ], [
                'content.required' => 'Comment content is required.',
                'content.max' => 'Comment content cannot exceed 5000 characters.',
                'comment_type.required' => 'Comment type is required.',
                'comment_type.in' => 'The comment type must be one of: ' . implode(', ', Comment::getTypes()) . '.',
            ]);

            // Find the internship
            $internship = Internship::findOrFail($internshipId);

            // Get the current user and their garant profile
            $user = Auth::user();
            if (!$user->hasAnyRole(['admin', 'garant'])) {
                return response()->json([
                    'message' => 'Only garants can add comments to internships.',
                ], 403);
            }

            $garant = Garant::where('user_id', $user->id)->first();
            if (!$garant) {
                return response()->json([
                    'message' => 'Garant profile not found. Please create a garant profile first.',
                    'help' => 'A user with garant/admin role must have a corresponding record in the garants table with user_id matching the user ID.',
                ], 404);
            }

            // Create the comment
            $comment = Comment::create([
                'internship_id' => $internshipId,
                'garant_id' => $garant->id,
                'content' => $validated['content'],
                'comment_type' => $validated['comment_type'],
            ]);

            // Load relationships for the response
            $comment->load(['garant.user']);

            // Log the comment creation
            Log::info('Comment created on internship', [
                'comment_id' => $comment->id,
                'internship_id' => $internshipId,
                'garant_id' => $garant->id,
                'garant_name' => $garant->full_name,
                'comment_type' => $comment->comment_type,
                'content_preview' => substr($comment->content, 0, 100),
            ]);

            // Return success response with created comment
            return response()->json([
                'message' => 'Comment added successfully.',
                'data' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'comment_type' => $comment->comment_type,
                    'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $comment->updated_at->format('Y-m-d H:i:s'),
                    'author' => [
                        'id' => $garant->id,
                        'name' => $garant->name,
                        'surname' => $garant->surname,
                        'full_name' => $garant->full_name,
                        'faculty' => $garant->faculty,
                        'email' => $garant->user->email ?? null,
                    ],
                ],
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error creating comment: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error creating comment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified comment.
     *
     * @param int $internshipId
     * @param int $commentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($internshipId, $commentId)
    {
        try {
            // Find the comment with its relationships
            $comment = Comment::where('id', $commentId)
                ->where('internship_id', $internshipId)
                ->with(['garant.user', 'internship'])
                ->firstOrFail();

            // Format the response
            $formattedComment = [
                'id' => $comment->id,
                'content' => $comment->content,
                'comment_type' => $comment->comment_type,
                'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                'updated_at' => $comment->updated_at->format('Y-m-d H:i:s'),
                'author' => [
                    'id' => $comment->garant->id,
                    'name' => $comment->garant->name,
                    'surname' => $comment->garant->surname,
                    'full_name' => $comment->garant->full_name,
                    'faculty' => $comment->garant->faculty,
                    'email' => $comment->garant->user->email ?? null,
                ],
                'internship' => [
                    'id' => $comment->internship->id,
                    'status' => $comment->internship->status,
                ],
            ];

            return response()->json([
                'message' => 'Comment retrieved successfully.',
                'data' => $formattedComment,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error retrieving comment: ' . $e->getMessage());
            return response()->json([
                'message' => 'Comment not found.',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified comment in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $internshipId
     * @param int $commentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $internshipId, $commentId)
    {
        try {
            // Validate the incoming request data
            $validated = $request->validate([
                'content' => [
                    'required',
                    'string',
                    'max:5000',
                ],
                'comment_type' => [
                    'required',
                    'string',
                    Rule::in(Comment::getTypes())
                ],
            ], [
                'content.required' => 'Comment content is required.',
                'content.max' => 'Comment content cannot exceed 5000 characters.',
                'comment_type.required' => 'Comment type is required.',
                'comment_type.in' => 'The comment type must be one of: ' . implode(', ', Comment::getTypes()) . '.',
            ]);

            // Find the comment
            $comment = Comment::where('id', $commentId)
                ->where('internship_id', $internshipId)
                ->firstOrFail();

            // Check if the current user is the author of the comment
            $user = Auth::user();
            $garant = Garant::where('user_id', $user->id)->first();
            
            if (!$garant || $comment->garant_id !== $garant->id) {
                return response()->json([
                    'message' => 'You can only edit your own comments.',
                ], 403);
            }

            // Store original content for logging
            $originalContent = $comment->content;
            $originalType = $comment->comment_type;

            // Update the comment
            $comment->update([
                'content' => $validated['content'],
                'comment_type' => $validated['comment_type'],
            ]);

            // Load relationships for the response
            $comment->load(['garant.user']);

            // Log the comment update
            Log::info('Comment updated on internship', [
                'comment_id' => $comment->id,
                'internship_id' => $internshipId,
                'garant_id' => $garant->id,
                'garant_name' => $garant->full_name,
                'old_type' => $originalType,
                'new_type' => $comment->comment_type,
                'content_changed' => $originalContent !== $comment->content,
            ]);

            // Return success response with updated comment
            return response()->json([
                'message' => 'Comment updated successfully.',
                'data' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'comment_type' => $comment->comment_type,
                    'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $comment->updated_at->format('Y-m-d H:i:s'),
                    'author' => [
                        'id' => $garant->id,
                        'name' => $garant->name,
                        'surname' => $garant->surname,
                        'full_name' => $garant->full_name,
                        'faculty' => $garant->faculty,
                        'email' => $garant->user->email ?? null,
                    ],
                ],
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error updating comment: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error updating comment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified comment from storage.
     *
     * @param int $internshipId
     * @param int $commentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($internshipId, $commentId)
    {
        try {
            // Find the comment
            $comment = Comment::where('id', $commentId)
                ->where('internship_id', $internshipId)
                ->with(['garant'])
                ->firstOrFail();

            // Check if the current user is the author of the comment or admin
            $user = Auth::user();
            $garant = Garant::where('user_id', $user->id)->first();
            
            if (!$user->hasAnyRole(['admin']) && (!$garant || $comment->garant_id !== $garant->id)) {
                return response()->json([
                    'message' => 'You can only delete your own comments.',
                ], 403);
            }

            // Store comment data for logging before deletion
            $commentData = [
                'comment_id' => $comment->id,
                'internship_id' => $internshipId,
                'garant_id' => $comment->garant_id,
                'garant_name' => $comment->garant->full_name,
                'comment_type' => $comment->comment_type,
                'content_preview' => substr($comment->content, 0, 100),
                'deleted_by' => $user->email,
            ];

            // Delete the comment
            $comment->delete();

            // Log the comment deletion
            Log::info('Comment deleted from internship', $commentData);

            return response()->json([
                'message' => 'Comment deleted successfully.',
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error deleting comment: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error deleting comment.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
